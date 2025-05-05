<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use Carbon\Carbon;

class ExpectedAttendanceDaysCount extends AttendanceVariable
{

    public function getResult(): int|float|bool
    {
        return collect($this->period)
            ->filter(fn(Carbon $date) => $this->isWorkingDay($date))
            ->count();
    }
}
