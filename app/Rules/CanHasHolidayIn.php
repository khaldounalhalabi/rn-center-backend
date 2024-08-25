<?php

namespace App\Rules;

use App\Models\Appointment;
use App\Models\ClinicHoliday;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CanHasHolidayIn implements ValidationRule
{
    private Carbon $startDate;
    private Carbon $endDate;
    private int $clinicId;

    /**
     * @param string $startDate
     * @param string $endDate
     */
    public function __construct(string $startDate, string $endDate, int $clinicId)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
        $this->clinicId = $clinicId;
    }


    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            Appointment::where('clinic_id', $this->clinicId)
                ->where('date', '>=', $this->startDate->format('Y-m-d'))
                ->where('date', '<=', $this->endDate->format('Y-m-d'))
                ->exists()
        ) {
            $fail("You cannot have holiday in this date , you have appointments in the selected period");
        }
    }
}
