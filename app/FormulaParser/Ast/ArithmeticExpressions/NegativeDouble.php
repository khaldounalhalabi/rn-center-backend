<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class NegativeDouble extends Expression
{
    public float $number;

    /**
     * @param float|string $number
     */
    public function __construct(float|string $number)
    {
        $this->number = -(doubleval($number));
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return new Result($this->number, []);
    }

    function __toString(): string
    {
        return "$this->number";
    }
}
