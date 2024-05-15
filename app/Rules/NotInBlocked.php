<?php

namespace App\Rules;

use App\Models\BlockedItem;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NotInBlocked implements ValidationRule
{
    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value)) {
            $blocked = BlockedItem::where('value', $value)->exists();

            if ($blocked) {
                $fail("$attribute is blocked you should use another one");
            }
        }
    }
}
