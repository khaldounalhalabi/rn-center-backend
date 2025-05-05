<?php

namespace App\FormulaParser\Ast\BooleanExpressions;

use App\FormulaParser\Ast\BooleanExpression;
use App\FormulaParser\Ast\BooleanFunction;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class BracedBooleanExpression
{
    private BooleanFunction|BooleanExpression $expression;

    /**
     * @param BooleanExpression|BooleanFunction $expression
     */
    public function __construct(BooleanFunction|BooleanExpression $expression)
    {
        $this->expression = $expression;
    }

    public function resolve(?int $userId, Formula $formula, Carbon|string $from, Carbon|string $to): Result
    {
        return $this->expression->resolve($userId, $formula, $from, $to);
    }

    public function __toString(): string
    {
        return "($this->expression)";
    }
}
