<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSystemOffer implements ValidationRule
{
    private ?int $customerId;

    public function __construct(?int $customerId = null)
    {
        if (auth()->user()?->isCustomer()) {
            $this->customerId = auth()->user()?->customer->id;
        }
    }

    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

    }
}
