<?php

namespace App\Rules;

use App\Models\SystemOffer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;

class SystemOfferBelongToClinic implements ValidationRule
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
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->clinicId) {
            $fail("Invalid Offer Data");
        }

        $belong = SystemOffer::where('id', $value)
            ->whereHas('clinics', function (Builder $builder) {
                $builder->where('id', $this->clinicId);
            })->active()->exists();

        if (!$belong) {
            $fail("$attribute is invalid");
        }
    }
}
