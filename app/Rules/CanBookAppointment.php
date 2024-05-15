<?php

namespace App\Rules;

use App\Models\Appointment;
use App\Models\Clinic;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CanBookAppointment implements ValidationRule
{
    private ?string $date;
    private ?string $from;
    private ?string $to;
    private ?int $customerId;

    private ?int $clinicId;
    private ?int $appointmentId;

    /**
     * @param string|null $date
     * @param string|null $from
     * @param string|null $to
     * @param int|null    $clinicId
     * @param int|null    $appointmentId
     * @param int|null    $customerId
     */
    public function __construct(?string $date, ?string $from, ?string $to, ?int $clinicId, ?int $appointmentId, ?int $customerId)
    {
        $this->date = $date;
        $this->from = $from;
        $this->to = $to;
        $this->clinicId = $clinicId;
        $this->appointmentId = $appointmentId;
        $this->customerId = $customerId;
    }


    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->appointmentId && !isset($this->date, $this->from, $this->to, $this->customerId, $this->clinicId)) {
            $fail("Invalid Data");
        }

        if ($this->appointmentId) {
            $appointment = Appointment::find($this->appointmentId);

            if (!$appointment) {
                $fail("Invalid Appointment ID");
            }

            $date = $this->date ?? $appointment->date->format('Y-m-d');
            $from = $this->from ?? $appointment->from->toTimeString();
            $to = $this->to ?? $appointment->to->toTimeString();

            if (!$appointment->clinic->canHasAppointmentIn($date, $from, $to, $appointment->customer_id)) {
                $fail("The doctor does not have a vacant slot at the specified date and time.");
            }
        } else {
            $clinic = Clinic::find($this->clinicId);

            if (!$clinic) {
                $fail("Invalid Clinic ID");
            }

            if (!$clinic->canHasAppointmentIn($this->date, $this->from, $this->to, $this->customerId)) {
                $fail("The doctor does not have a vacant slot at the specified date and time.");
            }
        }
    }
}
