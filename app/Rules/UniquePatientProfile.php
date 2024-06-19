<?php

namespace App\Rules;

use App\Models\PatientProfile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniquePatientProfile implements ValidationRule
{
    private ?int $clinicId;
    private ?int $customerId;
    private ?int $except;

    public function __construct(int|null $clinicId, int|null $customerId, int|null $except = null)
    {
        $this->customerId = $customerId;
        $this->clinicId = $clinicId;
        $this->except = $except;
    }

    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->clinicId) {
            $this->clinicId = auth()->user()?->getClinicId();
        }

        if (!$this->clinicId || !$this->customerId) {
            return;
        }

        $exists = PatientProfile::where('clinic_id', $this->clinicId)
            ->where('customer_id', $this->customerId)
            ->when($this->except, fn(Builder $query) => $query->where('id', '!=', $this->except))
            ->exists();

        if ($exists) {
            $fail("A patient can have one profile in a clinic , this patient already has a profile in this clinic");
        }
    }
}
