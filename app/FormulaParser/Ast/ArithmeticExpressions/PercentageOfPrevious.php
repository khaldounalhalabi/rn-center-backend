<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class PercentageOfPrevious extends Expression
{
    public Expression $prev;
    public Expression $percentage;

    /**
     * @param Expression $prev
     * @param Expression $percentage
     */
    public function __construct(Expression $prev, Expression $percentage)
    {
        $this->prev = $prev;
        $this->percentage = $percentage;
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $ofValue = $this->prev->resolve($userId, $formula, $from, $to);
        $percentage = $this->percentage->resolve($userId, $formula, $from, $to);

        return new Result(
            $ofValue->getResult() * ($percentage->getResult() / 100),
            array_merge($ofValue->getErrors(), $percentage->getErrors())
        );
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->percentage->getVariables(),
        ];
    }

    function __toString(): string
    {
        return "$this->percentage %";
    }
}
