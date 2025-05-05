<?php

namespace App\FormulaParser\SystemVariables;

use App\FormulaParser\SystemVariables\AttendanceVariables\AbsenceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\AbsenceHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\AttendanceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\ExpectedAttendanceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\ExpectedAttendanceHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\OvertimeDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\OvertimeHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\TotalAttendanceHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\TotalAttendanceHoursCountWithoutOvertimeHours;
use App\FormulaParser\SystemVariables\AttendanceVariables\TotalEveningOvertimeHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\TotalMorningOvertimeHoursCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Support\Collection;

abstract class SystemVariable
{
    public const ATTENDANCE_DAYS_COUNT = "attendance_days_count";
    public const ABSENCE_DAYS_COUNT = "absence_days_count";
    public const OVERTIME_DAYS_COUNT = "overtime_days_count";
    public const TOTAL_ATTENDANCE_HOURS_COUNT = "worked_hour";
    public const ABSENCE_HOURS_COUNT = "absence_hour";
    public const OVERTIME_HOURS_COUNT = "overtime_hour";
    public const EXPECTED_ATTENDANCE_HOURS_COUNT = "expected_pay_cycle_working_hour";
    public const EXPECTED_ATTENDANCE_DAYS_COUNT = "expected_pay_cycle_working_day";
    public const TOTAL_ATTENDANCE_HOURS_COUNT_WITHOUT_OVERTIME_HOURS = "total_attendance_hours_without_overtime_hour";
    public const TOTAL_MORNING_OVERTIME_HOURS_COUNT = "total_morning_overtime_hours_count";
    public const TOTAL_EVENING_OVERTIME_HOURS_COUNT = "total_evening_overtime_hours_count";

    public static array $keys = [
        self::TOTAL_ATTENDANCE_HOURS_COUNT_WITHOUT_OVERTIME_HOURS,
        self::ATTENDANCE_DAYS_COUNT,
        self::ABSENCE_DAYS_COUNT,
        self::OVERTIME_DAYS_COUNT,
        self::TOTAL_ATTENDANCE_HOURS_COUNT,
        self::ABSENCE_HOURS_COUNT,
        self::OVERTIME_HOURS_COUNT,
        self::EXPECTED_ATTENDANCE_HOURS_COUNT,
        self::EXPECTED_ATTENDANCE_DAYS_COUNT,
        self::TOTAL_MORNING_OVERTIME_HOURS_COUNT,
        self::TOTAL_EVENING_OVERTIME_HOURS_COUNT,
    ];

    abstract public function getResult(): int|float|bool;

    public static function factory(
        string                           $variableName,
        User                             $user,
        Collection|CollectionAlias|array $attendanceLogs,
        Carbon|string                    $from,
        Carbon|string                    $to
    ): SystemVariable|null
    {
        $attendanceVariableName = collect(self::$keys)
            ->filter(fn($variableSlug) => $variableName == $variableSlug)
            ->first();

        return match ($attendanceVariableName) {
            self::ATTENDANCE_DAYS_COUNT => new AttendanceDaysCount($user, $attendanceLogs, $from, $to),
            self::ABSENCE_DAYS_COUNT => new AbsenceDaysCount($user, $attendanceLogs, $from, $to),
            self::ABSENCE_HOURS_COUNT => new AbsenceHoursCount($user, $attendanceLogs, $from, $to),
            self::OVERTIME_HOURS_COUNT => new OvertimeHoursCount($user, $attendanceLogs, $from, $to),
            self::TOTAL_ATTENDANCE_HOURS_COUNT => new TotalAttendanceHoursCount($user, $attendanceLogs, $from, $to),
            self::OVERTIME_DAYS_COUNT => new OvertimeDaysCount($user, $attendanceLogs, $from, $to),
            self::EXPECTED_ATTENDANCE_DAYS_COUNT => new ExpectedAttendanceDaysCount($user, $attendanceLogs, $from, $to),
            self::EXPECTED_ATTENDANCE_HOURS_COUNT => new ExpectedAttendanceHoursCount($user, $attendanceLogs, $from, $to),
            self::TOTAL_ATTENDANCE_HOURS_COUNT_WITHOUT_OVERTIME_HOURS => new TotalAttendanceHoursCountWithoutOvertimeHours($user, $attendanceLogs, $from, $to),
            self::TOTAL_MORNING_OVERTIME_HOURS_COUNT => new TotalMorningOvertimeHoursCount($user, $attendanceLogs, $from, $to),
            self::TOTAL_EVENING_OVERTIME_HOURS_COUNT => new TotalEveningOvertimeHoursCount($user, $attendanceLogs, $from, $to),
            default => null
        };
    }
}
