<?php

namespace App\FormulaParser\Ast;

use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Result;
use App\Models\Formula;
use App\Models\FormulaVariable;
use Carbon\Carbon;

abstract class Expression
{
    /**
     * @var Identifier[]
     */
    protected array $variables = [];

    /**
     * @var ParsingError[]
     */
    protected static array $parsingFlags = [];

    /**
     * @param int|null      $userId
     * @param Formula       $formula
     * @param string|Carbon $from
     * @param string|Carbon $to
     * @return Result<float>
     */
    abstract function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result;

    abstract function __toString(): string;

    /**
     * @return Identifier[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string[]
     */
    public function getUniqueVariablesNames(): array
    {
        return array_values(array_unique(
            array_map(
                fn(Identifier $var) => $var->variable->slug,
                $this->getVariables()
            )
        ));
    }

    public function has(FormulaVariable $variable): bool
    {
        return (bool)count(
            array_filter($this->getVariables(), function ($var) use ($variable) {
                return get_class($var->variable) == get_class($variable)
                    && $var->variable->id == $variable->id;
            })
        );
    }

    /**
     * getting parsing errors flags will flush the errors buffer
     * @return ParsingError[]
     */
    public static function getParsingFlags(): array
    {
        $temp = self::$parsingFlags;
        self::$parsingFlags = [];
        return $temp;
    }

    public static function pushParsingFlag(ParsingError $item): void
    {
        self::$parsingFlags[] = $item;
    }
}
