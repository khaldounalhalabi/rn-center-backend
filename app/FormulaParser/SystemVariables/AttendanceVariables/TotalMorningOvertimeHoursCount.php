<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Models\AttendanceLog;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;

class TotalMorningOvertimeHoursCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $morningOvertimeMinutes = 0;

        $this->logsGroupedByDate->each(function (Collection $logs, string $date) use (&$morningOvertimeMinutes) {
            $dayOfWeek = strtolower(Carbon::parse($date)->dayName);
            $morningAttendanceMinutesInDay = 0;
            $logs = $logs->sortBy('attend_at')
                ->values();

            if ($this->isNoneWorkingDate($date)) {
                $logs->each(
                    $this->processAttendanceSlot($logs,
                        function (AttendanceLog $checkin, AttendanceLog $checkout) use (&$morningOvertimeMinutes) {
                            $morningOvertimeMinutes += $this->getMorningMinutesFromSlot($checkin->attend_at, $checkout->attend_at);
                        }
                    )
                );
                return true;
            }


            $logs->each(
                $this->processAttendanceSlot($logs,
                    function (AttendanceLog $checkin, AttendanceLog $checkout) use (&$morningAttendanceMinutesInDay) {
                        $morningAttendanceMinutesInDay += $this->getMorningMinutesFromSlot($checkin->attend_at, $checkout->attend_at);
                    }
                )
            );

            $requiredMorningMinutesInThisDay = $this->schedules
                ->groupBy('day_of_week')
                ->get($dayOfWeek)
                ?->map(fn(Schedule $schedule) => $this->getMorningMinutesFromSlot($schedule->start_time, $schedule->end_time))
                ->reduce(fn($carry, $item) => $carry + $item, 0) ?? 0;

            $morningOvertimeMinutes += abs(min($requiredMorningMinutesInThisDay - $morningAttendanceMinutesInDay, 0));

            return true;
        });

        return round(CarbonInterval::minute($morningOvertimeMinutes)->totalHours, 2);
    }

    public function getMorningMinutesFromSlot(Carbon $checkin, Carbon $checkout): int
    {
        $amStart = $checkin->copy()->startOfDay(); // Midnight
        $amEnd = $checkin->copy()->setTime(12, 0); // Noon

        $start = $checkin->greaterThanOrEqualTo($amStart) ? $checkin : $amStart;
        $end = $checkout->lessThanOrEqualTo($amEnd) ? $checkout : $amEnd;

        if ($start->lessThan($end)) {
            return $start->diffInMinutes($end);
        }
        return 0;
    }

}

