<?php

namespace App\Rules;

use App\Models\PatientProfile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniquePatientProfile implements ValidationRule
{
    private ?int $clinicId;
    private ?int $customerId;

    public function __construct(int|null $clinicId, int|null $customerId)
    {
        $this->customerId = $customerId;
        $this->clinicId = $clinicId;
    }

    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->clinicId) {
            $this->clinicId = auth()->user()?->clinic?->id;
        }

        if (!$this->clinicId || !$this->customerId) {
            return;
        }

        $exists = PatientProfile::where('clinic_id', $this->clinicId)
            ->where('customer_id', $this->customerId)
            ->exists();

        if ($exists) {
            $fail("A patient can have one profile in a clinic , this patient already has a profile in this clinic");
        }
    }
}
