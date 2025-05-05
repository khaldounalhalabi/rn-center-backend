<?php

namespace App\FormulaParser\Ast\BooleanExpressions;

use App\FormulaParser\Ast\BooleanExpression;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class LessThanOrEqual extends BooleanExpression
{
    function resolve(?int $userId, Formula $formula, Carbon|string $from, Carbon|string $to): Result
    {
        $leftResult = $this->left->resolve($userId, $formula, $from, $to);
        $rightResult = $this->right->resolve($userId, $formula, $from, $to);
        return new Result(
            $leftResult->getResult() <= $rightResult->getResult(),
            array_merge($leftResult->getErrors(), $rightResult->getErrors())
        );
    }

    public function __toString(): string
    {
        return "$this->left <= $this->right";
    }
}
