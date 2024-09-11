<?php

namespace App\Rules;

use App\Enums\ServiceStatusEnum;
use App\Repositories\ServiceRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveService implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $service = ServiceRepository::make()->find($value);

        if (!$service) {
            $fail("Invalid $attribute");
        }

        if ($service->status == ServiceStatusEnum::INACTIVE->value) {
            $fail("You've selected in-active service");
        }
    }
}
