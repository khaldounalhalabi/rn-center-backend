<?php

namespace App\FormulaParser\Ast;

use App\FormulaParser\Ast\BooleanExpressions\BracedBooleanExpression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class NegativeIFExpression extends Expression
{
    private BooleanExpression|BooleanFunction|BracedBooleanExpression $condition;
    private Expression $then;
    private Expression $else;

    /**
     * @param BooleanExpression|BooleanFunction|BracedBooleanExpression $condition
     * @param Expression                                                $then
     * @param Expression                                                $else
     */
    public function __construct(BooleanExpression|BooleanFunction|BracedBooleanExpression $condition, Expression $then, Expression $else)
    {
        $this->condition = $condition;
        $this->then = $then;
        $this->else = $else;
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables,
            ...$this->then->getVariables(),
            ...$this->else->getVariables()
        ];
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $conditionResult = $this->condition->resolve($userId, $formula, $from, $to);

        if ($conditionResult->getResult()) {
            $thenResult = $this->then->resolve($userId, $formula, $from, $to);
            return new Result(
                -($thenResult->getResult()),
                array_merge($conditionResult->getErrors(), $thenResult->getErrors())
            );
        } else {
            $elseResult = $this->else->resolve($userId, $formula, $from, $to);
            return new Result(
                -($elseResult->getResult()),
                array_merge($conditionResult->getErrors(), $elseResult->getErrors())
            );
        }
    }

    function __toString(): string
    {
        return "- IF($this->condition , $this->then, $this->else)";
    }
}
