<?php

namespace App\FormulaParser\Ast;

use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

abstract class ExpressionOperation extends Expression
{
    public Expression $left;
    public Expression $right;

    /**
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct(Expression $left, Expression $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        return $this->resolve($userId, $formula, $from, $to);
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->left->getVariables(), ...$this->right->getVariables(),
        ];
    }

    public function __toString(): string
    {
        return "";
    }
}
