<?php

namespace App\Rules;

use App\Models\Offer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ClinicOfferBelongToClinic implements ValidationRule
{
    private ?int $clinicId;

    /**
     * @param int|null $clinicId
     */
    public function __construct(?int $clinicId)
    {
        $this->clinicId = $clinicId;
    }


    /**
     * Run the validation rule.
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->clinicId) {
            $fail("Invalid Offer Data");
        }

        $belongs = Offer::where('clinic_id', $this->clinicId)
            ->isActive()
            ->exists();

        if (!$belongs) {
            $fail("$attribute is invalid");
        }
    }
}
