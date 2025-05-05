<?php

namespace App\FormulaParser\Ast\Functions;

use App\FormulaParser\Ast\BooleanFunction;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class NotFunction extends BooleanFunction
{
    function resolve(?int $userId, Formula $formula, Carbon|string $from, Carbon|string $to): Result
    {
        $final = true;
        $errors = [];

        foreach ($this->booleanExpressions as $booleanExpression) {
            $result = $booleanExpression->resolve($userId, $formula, $from, $to);
            $final = $final && $result->getResult();
            $errors = array_merge($errors, $result->getErrors());
        }

        return new Result(!$final, $errors);
    }

    public function __toString(): string
    {
        return "NOT(" . implode(", ", $this->booleanExpressions) . ")";
    }
}
