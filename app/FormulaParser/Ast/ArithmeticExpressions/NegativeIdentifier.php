<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;
use Exception;

class NegativeIdentifier extends Expression
{
    public Identifier $variable;

    /**
     * @param Identifier $variable
     */
    public function __construct(Identifier $variable)
    {
        $this->variable = $variable;
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables,
            $this->variable,
        ];
    }

    /**
     * @throws Exception
     */
    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $result = $this->variable->resolve($userId, $formula, $from, $to);
        return new Result(-$result->getResult(), $result->getErrors());
    }

    function __toString(): string
    {
        return "- $this->variable";
    }
}
