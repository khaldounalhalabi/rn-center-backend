<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use Illuminate\Support\Collection;

class OvertimeDaysCount extends AttendanceVariable
{
    public function getResult(): int
    {
        return $this->logsGroupedByDate
            ->filter(fn(Collection $logs, string $date) => $this->isNoneWorkingDate($date))
            ->count();
    }
}
