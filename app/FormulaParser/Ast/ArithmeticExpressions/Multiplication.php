<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\ExpressionOperation;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Multiplication extends ExpressionOperation
{
    public function __construct(Expression $left, Expression $right)
    {
        parent::__construct($left, $right);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $left = $this->left->resolve($userId, $formula, $from, $to);
        $right = $this->right->resolve($userId, $formula, $from, $to);

        return new Result(
            $left->getResult() * $right->getResult(),
            array_merge($left->getErrors(), $right->getErrors())
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
        return "$this->left * $this->right";
    }
}
