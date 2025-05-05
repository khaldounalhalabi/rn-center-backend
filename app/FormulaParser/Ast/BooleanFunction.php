<?php

namespace App\FormulaParser\Ast;

use App\FormulaParser\Ast\BooleanExpressions\BracedBooleanExpression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class BooleanFunction extends Expression
{
    /**
     * @var BooleanExpression|BracedBooleanExpression[]
     */
    protected array $booleanExpressions;

    /**
     * @param BooleanExpression[]|BracedBooleanExpression[] $booleanExpressions
     */
    public function __construct(array $booleanExpressions)
    {
        $this->booleanExpressions = $booleanExpressions;
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return $this->resolve($userId, $formula, $from, $to);
    }

    public function __toString(): string
    {
        return " ( " . implode(", ", $this->booleanExpressions) . ")";
    }
}
