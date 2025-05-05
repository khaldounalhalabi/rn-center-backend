<?php

namespace App\FormulaParser;

use App\FormulaParser\Errors\ParsingError;

/**
 * @template T of int|float|bool
 */
class Result
{
    /** @var int|float|bool */
    private int|float|bool $result;

    /** @var ParsingError[] */
    private array $errors;

    /**
     * @param bool|float|int $result
     * @param array          $errors
     */
    public function __construct(float|bool|int $result = 0 , array $errors = [])
    {
        $this->result = $result;
        $this->errors = $errors;
    }

    /**
     * @return int|float|bool
     */
    public function getResult(): float|bool|int
    {
        return $this->result;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __toString(): string
    {
        return "$this->result";
    }
}
