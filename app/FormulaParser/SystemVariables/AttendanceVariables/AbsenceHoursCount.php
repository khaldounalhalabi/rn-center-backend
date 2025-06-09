<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Models\AttendanceLog;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Collection as EQCollection;
use Illuminate\Support\Collection;

class AbsenceHoursCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $absenceMinutes = 0;

        foreach ($this->period as $workDate) {
            $dayName = strtolower($workDate->dayName);
            /** @var EQCollection<Schedule> $scheduleInDate */
            $scheduleInDate = $this->schedules->groupBy('day_of_week')->get($dayName);

            /** @var Collection<AttendanceLog>|array<AttendanceLog> $attendanceInDate */
            $attendanceInDate = $this->attendanceLogs
                ->filter(fn(AttendanceLog $log) => $log->attend_at->format('Y-m-d') == $workDate->format('Y-m-d'))
                ->sortBy('attend_at')
                ->values();

            $requiredMinutesInDay = $scheduleInDate?->reduce(function ($carry, Schedule $schedule) {
                return $carry + $schedule->start_time->diffInMinutes($schedule->end_time);
            }, 0) ?? 0;

            if ($attendanceInDate->isEmpty() && $this->isWorkingDay($workDate)) {
                $absenceMinutes += $requiredMinutesInDay;
            } elseif ($attendanceInDate->isNotEmpty() && $this->isWorkingDay($workDate)) {
                $attendanceMinutesInDay = 0;
                $attendanceInDate->each(function (AttendanceLog $checkin, $index) use ($workDate, &$attendanceMinutesInDay, $attendanceInDate) {
                    if (!$checkin->isCheckin()) {
                        return true;
                    }

                    /** @var null|AttendanceLog $checkout */
                    $checkout = $attendanceInDate->get($index + 1);

                    if (!$checkout?->isCheckout()) {
                        return true;
                    }

                    $attendanceMinutesInDay += $checkin->attend_at->diffInMinutes($checkout->attend_at);
                    return true;
                });

                $absenceMinutes += max($requiredMinutesInDay - $attendanceMinutesInDay, 0);
            }
        }

        return abs(round($absenceMinutes / 60, 2));
    }
}
