<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\ExpressionOperation;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Subtraction extends ExpressionOperation
{
    public function __construct(Expression $left, Expression $right)
    {
        parent::__construct($left, $right);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $leftResult = $this->left->resolve($userId, $formula, $from, $to);
        $rightResult = $this->right->resolve($userId, $formula, $from, $to);
        return new Result(
            $leftResult->getResult() - $rightResult->getResult(),
            array_merge($leftResult->getErrors(), $rightResult->getErrors())
        );
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->left->getVariables(), ...$this->right->getVariables(),
        ];
    }

    public function __toString(): string
    {
        return "$this->left - $this->right";
    }
}
