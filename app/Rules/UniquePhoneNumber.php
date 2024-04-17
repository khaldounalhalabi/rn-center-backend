<?php

namespace App\Rules;

use App\Models\PhoneNumber;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniquePhoneNumber implements ValidationRule
{
    private int $phoneableId;

    /** @var class-string */
    private string $phoneableType;

    /**
     * @param int $phoneableId
     * @param class-string $type
     */
    public function __construct(int $phoneableId, string $type = User::class)
    {
        $this->phoneableId = $phoneableId;
        $this->phoneableType = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phones = PhoneNumber::where('phoneable_type', $this->phoneableType)
            ->where('phoneable_id', $this->phoneableId)
            ->pluck('id')->toArray();

        $exists = PhoneNumber::where('phone', $value)
            ->whereNotIn('id', $phones)
            ->exists();

        if ($exists) {
            $fail("Another User Or Hospital Has The Same $attribute");
        }
    }
}
