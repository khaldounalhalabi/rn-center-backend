<?php

namespace App\FormulaParser\Ast\ArithmeticExpressions;

use App\Enums\FormulaParsingFlagEnum;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\ExpressionOperation;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Result;
use App\Models\Formula;
use Carbon\Carbon;

class Division extends ExpressionOperation
{
    public function __construct(Expression $left, Expression $right)
    {
        parent::__construct($left, $right);
    }

    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        $right = $this->right->resolve($userId, $formula, $from, $to);
        $left = $this->left->resolve($userId, $formula, $from, $to);
        $errors = array_merge($right->getErrors(), $left->getErrors());
        if ($right->getResult() == 0) {
            $errors[] = new ParsingError(
                FormulaParsingFlagEnum::DIVIDING_BY_ZERO,
                "The formula performed a zero division in the formula named : [$formula->name] while processing the dates between : [$from , $to] caused by this : [{$this->right}] Segment: [$this->left / $this->right]",
            );

            return new Result(0, $errors);
        }
        return new Result($left->getResult() / $right->getResult(), $errors);
    }

    public function getVariables(): array
    {
        return [
            ...$this->variables, ...$this->left->getVariables(), ...$this->right->getVariables(),
        ];
    }

    function __toString(): string
    {
        return "$this->left / $this->right";
    }
}
