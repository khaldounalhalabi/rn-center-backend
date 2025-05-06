<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Models\Schedule;
use Ramsey\Collection\Collection;

//TODO:: this is giving wrong values
class ExpectedAttendanceHoursCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $expectedHours = [];

        /**
         * @var string               $dayName
         * @var Collection<Schedule> $workingHours
         * @noinspection PhpLoopCanBeConvertedToArrayMapInspection
         */
        foreach ($this->schedules->groupBy('day_of_week') as $dayName => $workingHours) {
            $expectedHours[$dayName] = $workingHours->reduce(function ($carry, Schedule $schedule) {
                return $carry + round($schedule->start_time?->diffInMinutes($schedule->end_time) / 60, 2);
            }, 0);
        }

        $totalHours = 0;

        foreach ($this->period as $date) {
            if (isset($expectedHours[strtolower($date->dayName)])) {
                $totalHours += $expectedHours[strtolower($date->dayName)];
            }
        }

        return $totalHours;
    }
}
