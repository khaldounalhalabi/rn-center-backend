<?php

namespace App\FormulaParser\Ast\Terminals;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Integer extends Expression
{
    public string|int $number;

    /**
     * @param int|string $number
     */
    public function __construct(int|string $number)
    {
        $this->number = intval($number);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return new Result($this->number);
    }

    public function __toString(): string
    {
        return "$this->number";
    }
}
