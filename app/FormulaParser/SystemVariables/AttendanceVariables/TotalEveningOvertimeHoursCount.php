<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Models\AttendanceLog;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;

class TotalEveningOvertimeHoursCount extends AttendanceVariable
{

    public function getResult(): int|float|bool
    {
        $eveningOvertimeMinutes = 0;

        $this->logsGroupedByDate->each(function (Collection $logs, string $date) use (&$eveningOvertimeMinutes) {
            $dayOfWeek = strtolower(Carbon::parse($date)->dayName);
            $eveningAttendanceMinutesInDay = 0;
            $logs = $logs->sortBy('attend_at')
                ->values();

            if ($this->isNoneWorkingDate($date)) {
                $logs->each(
                    $this->processAttendanceSlot($logs,
                        function (AttendanceLog $checkin, AttendanceLog $checkout) use (&$eveningOvertimeMinutes) {
                            $eveningOvertimeMinutes += $this->getEveningMinutesFromSlot($checkin->attend_at, $checkout->attend_at);
                        }
                    )
                );
                return true;
            }

            $logs->each(
                $this->processAttendanceSlot($logs,
                    function (AttendanceLog $checkin, AttendanceLog $checkout) use (&$eveningAttendanceMinutesInDay) {
                        $eveningAttendanceMinutesInDay += $this->getEveningMinutesFromSlot($checkin->attend_at, $checkout->attend_at);
                    }
                )
            );

            $requiredEveningMinutesInThisDay = $this->schedules
                ->groupBy('day_of_week')
                ->get($dayOfWeek)
                ?->map(fn(Schedule $schedule) => $this->getEveningMinutesFromSlot($schedule->start_time, $schedule->end_time))
                ->reduce(fn($carry, $item) => $carry + $item, 0) ?? 0;

            $eveningOvertimeMinutes += abs(min($requiredEveningMinutesInThisDay - $eveningAttendanceMinutesInDay, 0));

            return true;
        });

        return round(CarbonInterval::minute($eveningOvertimeMinutes)->totalHours, 2);
    }

    public function getEveningMinutesFromSlot(Carbon $checkin, Carbon $checkout): int
    {
        $pmStart = $checkin->copy()->setTime(12, 0);
        $pmEnd = $checkin->copy()->endOfDay();
        $start = $checkin->greaterThanOrEqualTo($pmStart) ? $checkin : $pmStart;
        $end = $checkout->lessThanOrEqualTo($pmEnd) ? $checkout : $pmEnd;

        if ($start->lessThan($end)) {
            return $start->diffInMinutes($end);
        }

        return 0;
    }
}
