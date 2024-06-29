<?php

namespace App\Rules;

use App\Models\Customer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Translation\PotentiallyTranslatedString;

class CustomerBelongToClinic implements ValidationRule
{
    private $clinicId;

    /**
     * @param $clinicId
     */
    public function __construct($clinicId)
    {
        $this->clinicId = $clinicId;
    }


    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!auth()->user()?->isClinic()) {
            return;
        }

        $patientProfile = Customer::where('id', $value)
            ->whereHas('patientProfiles', function (Builder $builder) {
                $builder->where('clinic_id', $this->clinicId);
            })->exists();

        if (!$patientProfile) {
            $fail("The Selected Customer Doesn't Belongs To Your Clinic");
        }
    }
}
