<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\ExpressionOperation;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Addition extends ExpressionOperation
{
    public function __construct(Expression $left, Expression $right)
    {
        parent::__construct($left, $right);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $firstResult = $this->left->resolve($userId, $formula, $from, $to);
        $secondResult = $this->right->resolve($userId, $formula, $from, $to);
        return new Result(
            $firstResult->getResult() + $secondResult->getResult(),
            array_merge($firstResult->getErrors(), $secondResult->getErrors())
        );
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->left->getVariables(), ...$this->right->getVariables(),
        ];
    }

    function __toString(): string
    {
        return "$this->left + $this->right";
    }
}
