<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\Enums\AttendanceLogTypeEnum;
use App\Models\AttendanceLog;
use Carbon\CarbonInterval;

class TotalAttendanceHoursCount extends AttendanceVariable
{
    public function getResult(): int|float|bool
    {
        $wholePayCycleWorkingMinutes = 0;
        $checkInTime = null;
        $this->attendanceLogs->each(function (AttendanceLog $attendanceLog) use (&$checkInTime, &$wholePayCycleWorkingMinutes) {
            if ($attendanceLog->isCheckin()) {
                $checkInTime = $attendanceLog->attend_at;
            } else {
                $wholePayCycleWorkingMinutes += !is_null($checkInTime)
                    ? $attendanceLog->attend_at->diffInMinutes($checkInTime)
                    : 0;
            }
        });
        return round(CarbonInterval::minute($wholePayCycleWorkingMinutes)->totalHours, 2);
    }
}
