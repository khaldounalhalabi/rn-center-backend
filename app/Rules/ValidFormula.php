<?php

namespace App\Rules;

use App\FormulaParser\EquationParser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\ValidationException;

class ValidFormula implements ValidationRule
{
    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            EquationParser::parse($value);
        } catch (ValidationException $exception) {
            $fail($exception->getMessage());
        }
    }
}
