<?php

namespace App\FormulaParser\Ast\Terminals;

use App\Enums\FormulaParsingFlagEnum;
use App\Exceptions\FormulaErrorException;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Result;
use App\Models\Formula;
use App\Models\FormulaVariable;
use App\Traits\IdentifierResolver;
use Carbon\Carbon;
use Exception;

class Identifier extends Expression
{
    use IdentifierResolver;

    public string $name;
    public int $startIndex;
    public int $stopIndex;
    public FormulaVariable $variable;

    /**
     * @param string          $name
     * @param FormulaVariable $variable
     * @param int             $startIndex
     * @param int             $stopIndex
     */
    public function __construct(string $name, FormulaVariable $variable, int $startIndex, int $stopIndex)
    {
        $this->name = $name;
        $this->variable = $variable;
        $this->startIndex = $startIndex;
        $this->stopIndex = $stopIndex;
    }

    /**
     * @throws Exception
     */
    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        try {
            return new Result($this->resolveSystemVariables($userId, $formula, $from, $to));
        } catch (FormulaErrorException $exception) {
            return new Result(0, [
                new ParsingError($exception->errorFlag, $exception->message)
            ]);
        } catch (Exception $exception) {
            return new Result(0, [
                new ParsingError(FormulaParsingFlagEnum::UNKNOWN, $exception->getMessage())
            ]);
        }
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables,
            $this,
        ];
    }

    public function __toString(): string
    {
        return "$this->name";
    }
}
