<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneNumber implements ValidationRule
{
    private $userId;

    public function __construct($userId){
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = \App\Models\PhoneNumber::where('phone' , $value)
        ->where('phoneable_id' , '!=' , $this->userId)
        ->where('phoneable_type' , \App\Models\User::class)
        ->exists();

        if ($exists) {
            $fail("Another User Has The Same $attribute");
        }
    }
}
