<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class NegativeExpression extends Expression
{
    public Expression $expression;

    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    public function getVariables(): array
    {
        return [...$this->variables, ...$this->expression->getVariables()];
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $result = $this->expression->resolve($userId, $formula, $from, $to);
        return new Result(-$result->getResult(), $result->getErrors());
    }

    function __toString(): string
    {
        return "- $this->expression";
    }
}
