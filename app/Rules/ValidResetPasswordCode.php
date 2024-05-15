<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidResetPasswordCode implements ValidationRule
{
    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('reset_password_code', $value)->first();
        if ($user->updated_at->addMinutes(10)->equalTo(now())) {
            $fail("Your Reset Password Code Has Expired Request New One");
        }
    }
}
