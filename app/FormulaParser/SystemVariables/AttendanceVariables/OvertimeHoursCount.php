<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Models\AttendanceLog;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection as EQCollection;
use Illuminate\Support\Collection;

class OvertimeHoursCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $overTimeMinutes = 0;

        $this->logsGroupedByDate->each(function (Collection $logs, string $date) use (&$overTimeMinutes) {
            $dayOfWeek = strtolower(Carbon::parse($date)->dayName);
            $attendanceInDayPerMinute = 0;

            if ($this->isNoneWorkingDate($date)) {
                $logs->sortBy('attend_at')
                    ->values()
                    ->each(function (AttendanceLog $checkin, $index) use ($date, $logs, &$overTimeMinutes) {
                        if (!$checkin->isCheckin()) {
                            return true;
                        }
                        /** @var AttendanceLog $checkout */
                        $checkout = $logs->get($index + 1);

                        if (!$checkout?->isCheckout()) {
                            return true;
                        }

                        $overTimeMinutes += $checkin->attend_at->diffInMinutes($checkout->attend_at);
                        return true;
                    });
                return true;
            } else {
                $logs->sortBy('attend_at')
                    ->values()
                    ->each(function (AttendanceLog $checkin, $index) use ($logs, &$attendanceInDayPerMinute, &$overTimeMinutes) {
                        if (!$checkin->isCheckin()) {
                            return true;
                        }

                        /** @var AttendanceLog $checkout */
                        $checkout = $logs->get($index + 1);

                        if (!$checkout?->isCheckout()) {
                            return true;
                        }
                        $attendanceInDayPerMinute += $checkin->attend_at->diffInMinutes($checkout->attend_at);
                        return true;
                    });
            }

            /** @var EQCollection<Schedule> $scheduleInDate */
            $scheduleInDate = $this->schedules->groupBy('day_of_week')->get($dayOfWeek);
            $requiredMinutesInThisDay = $scheduleInDate?->reduce(function ($carry, Schedule $schedule) {
                return $carry + $schedule->start_time->diffInMinutes($schedule->end_time);
            }, 0) ?? 0;

            $overTimeMinutes += abs(min($requiredMinutesInThisDay - $attendanceInDayPerMinute, 0));
            return true;
        });

        return round(CarbonInterval::minute($overTimeMinutes)->totalHours, 2);
    }
}
