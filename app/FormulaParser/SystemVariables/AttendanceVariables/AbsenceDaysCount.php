<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AbsenceDaysCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $attendedDays = $this->logsGroupedByDate
            ->map(fn(Collection $logs, string $date) => $date)
            ->toArray();

        return collect($this->period)
            ->filter(fn(Carbon $date) => $this->isWorkingDay($date)
                && !in_array(
                    $date->format('Y-m-d'),
                    $attendedDays
                )
            )->count();
    }
}
