<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class NegativeInteger extends Expression
{
    public int $number;

    /**
     * @param int|string $number
     */
    public function __construct(int|string $number)
    {
        $this->number = -intval($number);
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
