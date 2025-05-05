<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

class TotalAttendanceHoursCountWithoutOvertimeHours extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $totalAttendance = new TotalAttendanceHoursCount(
            $this->user,
            $this->attendanceLogs,
            $this->from,
            $this->to
        );
        $totalOvertime = new OvertimeHoursCount(
            $this->user,
            $this->attendanceLogs,
            $this->from,
            $this->to
        );
        return ($totalAttendance->getResult() - $totalOvertime->getResult());
    }
}
