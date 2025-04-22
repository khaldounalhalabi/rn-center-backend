<?php

namespace App\Services;

use App\Models\Schedule;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\HolidayRepository;
use App\Traits\Makable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailableAppointmentTimeService
{
    use Makable;

    /**
     * Get available appointment time slots for a clinic on a specific date
     * @param int    $clinicId The clinic to check availability for
     * @param string $date     The date to check in Y-m-d format
     * @return Collection Collection of available time slots as Carbon instances
     */
    public function getAvailableTimeSlots(int $clinicId, string $date): Collection
    {
        $clinic = ClinicRepository::make()->find($clinicId);
        if (!$clinic) {
            return collect();
        }

        $dateCarbon = Carbon::parse($date);
        if (HolidayRepository::make()->isHoliday($date)) {
            return collect();
        }
        $dayName = strtolower($dateCarbon->englishDayOfWeek);
        $schedules = $clinic->schedules()->where('day_of_week', $dayName)->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $allPossibleSlots = $this->generateAllPossibleSlots($schedules, $date);

        $bookedAppointments = AppointmentRepository::make()->getByDate(
            $date,
            null,
            $clinic->id,
        );
        if ($bookedAppointments->count() >= $clinic->max_appointments) {
            return collect();
        }

        // Filter out time slots that overlap with existing appointments
        return $this->filterAvailableSlots($allPossibleSlots, $bookedAppointments);
    }

    /**
     * Generate all possible time slots from clinic schedules
     * @param Collection<Schedule> $schedules
     * @param string               $date
     * @return Collection
     */
    private function generateAllPossibleSlots(Collection $schedules, string $date): Collection
    {
        $slots = collect();
        $appointmentDuration = config('appointments.duration_minutes', 30);

        foreach ($schedules as $schedule) {
            $startTime = Carbon::parse($date . ' ' . $schedule->start_time->format('H:i:s'));
            $endTime = Carbon::parse($date . ' ' . $schedule->end_time->format('H:i:s'));

            // Generate time slots at regular intervals
            while ($startTime->copy()->addMinutes($appointmentDuration)->lte($endTime)) {
                $slots->push(clone $startTime);
                $startTime->addMinutes($appointmentDuration);
            }
        }

        return $slots->sort();
    }

    /**
     * Filter available time slots based on booked appointments
     * @param Collection $allPossibleSlots
     * @param Collection $bookedAppointments
     * @return Collection
     */
    private function filterAvailableSlots(Collection $allPossibleSlots, Collection $bookedAppointments): Collection
    {
        $appointmentDuration = config('appointments.duration_minutes', 30);

        return $allPossibleSlots->filter(function (Carbon $slot) use ($bookedAppointments, $appointmentDuration) {
            $slotStart = $slot;
            $slotEnd = $slot->copy()->addMinutes($appointmentDuration);

            // Check if this slot overlaps with any booked appointment
            foreach ($bookedAppointments as $appointment) {
                $appointmentTime = Carbon::parse($appointment->date_time);
                $appointmentEnd = $appointmentTime->copy()->addMinutes($appointmentDuration);

                // Check for any overlap between slot and appointment
                if (
                    // Slot starts during an appointment
                    ($slotStart >= $appointmentTime && $slotStart < $appointmentEnd) ||
                    // Slot ends during an appointment
                    ($slotEnd > $appointmentTime && $slotEnd <= $appointmentEnd) ||
                    // Appointment starts during the slot
                    ($appointmentTime >= $slotStart && $appointmentTime < $slotEnd) ||
                    // Exact match
                    ($slotStart == $appointmentTime)
                ) {
                    return false;
                }
            }

            return true;
        });
    }
}
