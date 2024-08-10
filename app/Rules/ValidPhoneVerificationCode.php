<?php

namespace App\Rules;

use App\Models\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidPhoneVerificationCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phoneNumber = PhoneNumber::where('verification_code', $value)->first();
        if ($phoneNumber->updated_at->addMinutes(15)->equalTo(now())) {
            $fail("Your $attribute Has Expired Request New One");
        }
    }
}
