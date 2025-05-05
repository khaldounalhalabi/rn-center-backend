<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

class AttendanceDaysCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        return $this->attendanceDaysCount;
    }
}
