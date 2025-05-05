<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class BracedExpression extends Expression
{
    public Expression $expression;

    /**
     * @param Expression $expression
     */
    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return $this->expression->resolve($userId, $formula, $from, $to);
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->expression->getVariables(),
        ];
    }

    function __toString(): string
    {
        return "($this->expression)";
    }
}
