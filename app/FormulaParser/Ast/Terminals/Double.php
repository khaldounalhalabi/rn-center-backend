<?php

namespace App\FormulaParser\Ast\Terminals;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Double extends Expression
{
    public float $number;

    /**
     * @param float|string $number
     */
    public function __construct(float|string $number)
    {
        $this->number = (double)($number);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return new Result($this->number, []);
    }

    public function __toString(): string
    {
        return "$this->number";
    }
}
