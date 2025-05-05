<?php

namespace App\FormulaParser;

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\InputStream;
use App\Enums\FormulaParsingFlagEnum;
use App\Exceptions\FormulaSyntaxException;
use App\FormulaParser\AntlrParser\ErrorListener;
use App\FormulaParser\AntlrParser\FormulaLexer;
use App\FormulaParser\AntlrParser\FormulaParser;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Visitors\FormulaVisitor;
use App\Models\Formula;
use App\Models\FormulaSegment;
use App\Models\FormulaVariable;
use Error;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class EquationParser
{
    public static function parse(string $input): ?Expression
    {
        try {
            // to flush the previous errors on every parse operation
            Expression::getParsingFlags();
            $inputSteam = InputStream::fromString($input);
            $lexer = new FormulaLexer($inputSteam);
            $tokens = new CommonTokenStream($lexer);
            $parser = new FormulaParser($tokens);
            $parser->addErrorListener(new ErrorListener());
            $tree = $parser->expression();
            $visitor = new FormulaVisitor();
            return $visitor->visit($tree);
        } catch (FormulaSyntaxException $exception) {
            $errorPos = self::locateErrorInInput($input, $exception->line, $exception->position);
            if (app()->runningInConsole() && !app()->environment('testing')) {
                Expression::pushParsingFlag(new ParsingError(
                    FormulaParsingFlagEnum::FORMULA_SYNTAX,
                    "error while parsing formula : ($input) : \n " . $exception->getMessage() . " [position in string : $errorPos]",
                ));
            } else {
                throw ValidationException::withMessages([
                    'formula' => [
                        'message' => $exception->getMessage() . " [position in string : $errorPos]",
                    ],
                ]);
            }
            return null;
        } catch (Exception|Error|Throwable $exception) {
            Expression::pushParsingFlag(
                new ParsingError(
                    FormulaParsingFlagEnum::UNKNOWN,
                    "Unknown error while parsing formula : ($input) , please contact support",
                )
            );

            logger()->info("Error in formula : [$input] \n Error message: [{$exception->getMessage()}]\n Line: {$exception->getLine()}\n File: {$exception->getFile()}\n");

            return null;
        }
    }

    private static function locateErrorInInput(string $input, int $line, int $col): int
    {
        $input = explode("\n", $input);
        $errorPosInInputString = 0;
        foreach ($input as $key => $item) {
            if (($key + 1) != $line) {
                $errorPosInInputString += Str::length($item);
            }

            if (($key + 1) == $line) {
                $errorPosInInputString += $col;
                break;
            }
        }

        return $errorPosInInputString;
    }


    /**
     * @throws Exception
     */
    public static function replaceInFormula(Formula|FormulaSegment $payFormula, FormulaVariable $search, $replace): string
    {
        $formula = $payFormula instanceof Formula ? $payFormula->formula : $payFormula->segment;
        if (!$formula) {
            throw new Exception("When replacing values in formulas there should be a valid formula");
        }

        try {
            $expression = self::parse($formula);
            if (!$expression) {
                foreach (Expression::getParsingFlags() as $parsingFlag) {
                    logger()->info($parsingFlag);
                }
                throw new Exception("There is been an error parsing formula : $formula");
            }
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages([
                'formula' => [
                    'message' => "You Need To Fix The Formula [$payFormula->name] In Order To Complete The Operation" . " \n " . $exception->getMessage(),
                ],
            ]);
        }
        $changedVariables = array_filter(
            $expression->getVariables(),
            fn(Identifier $var) => (get_class($var->variable) == get_class($search)) && ($var->variable->id == $search->id)
        );

        $positions = collect();
        foreach ($changedVariables as $changedVariable) {
            $positions->push([
                'start' => $changedVariable->startIndex,
                'length' => ($changedVariable->stopIndex - $changedVariable->startIndex) + 1,
                'replace' => $replace,
            ]);
        }

        $positions->sortByDesc('start')
            ->each(function ($pos) use (&$formula) {
                $formula = substr_replace(
                    string: $formula,
                    replace: $pos['replace'],
                    offset: $pos['start'],
                    length: $pos['length']
                );
            });

        return $formula;
    }
}
