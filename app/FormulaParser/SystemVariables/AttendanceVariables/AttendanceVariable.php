<?php

namespace App\FormulaParser\SystemVariables\AttendanceVariables;

use App\FormulaParser\SystemVariables\SystemVariable;
use App\Models\AttendanceLog;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Support\Collection;

abstract class AttendanceVariable extends SystemVariable
{
    protected User $user;
    protected Collection|array|CollectionAlias $attendanceLogs;
    protected Carbon $from;
    protected Carbon $to;
    protected CarbonPeriod $period;
    protected int $attendanceDaysCount;

    /** @var Collection<Collection<AttendanceLog>|CollectionAlias<AttendanceLog>|array<AttendanceLog>> */
    protected Collection $logsGroupedByDate;

    /** @var CollectionAlias<Schedule> */
    protected CollectionAlias $schedules;

    /**
     * @param User                                                                          $user
     * @param Collection<AttendanceLog>|CollectionAlias<AttendanceLog>|array<AttendanceLog> $attendanceLogs
     * @param Carbon|string                                                                 $from
     * @param Carbon|string                                                                 $to
     */
    public function __construct(User $user, Collection|CollectionAlias|array $attendanceLogs, Carbon|string $from, Carbon|string $to)
    {
        $this->user = $user;
        $this->attendanceLogs = $attendanceLogs->sortBy('attend_at');
        $this->logsGroupedByDate = $this->attendanceLogs->groupBy(fn(AttendanceLog $attendanceLog) => $attendanceLog->attend_at->format('Y-m-d'));
        $this->attendanceDaysCount = $this->logsGroupedByDate->count();
        $this->schedules = $this->user->getSchedules()->get();
        $this->from = $from instanceof Carbon ? $from : Carbon::parse($from);
        $this->to = $to instanceof Carbon ? $to : Carbon::parse($to);
        $this->period = CarbonPeriod::create($this->from->format('Y-m-d'), $this->to->format('Y-m-d'));
    }

    protected function isNoneWorkingDate(Carbon|string $date): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $scheduleDays = $this->schedules->groupBy('day_of_week')->keys()->toArray();

        //TODO::handle center holidays and doctor holidays
        return !in_array(
            strtolower($date->dayName),
            $scheduleDays
        );
    }

    protected function isWorkingDay(Carbon|string $date): bool
    {
        $scheduleDays = $this->schedules->groupBy('day_of_week')->keys()->toArray();

        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // TODO::handle center holidays and doctor holidays
        // this means if the saturday and friday are none-working days then the working on them will be counted as an overtime day
        return in_array(strtolower($date->dayName), $scheduleDays);
    }

    /**
     * the processFunction takes 2 parameters of type \App\Models\AttendanceLog
     * if you want to break return false from the processFunction
     * @param Collection<AttendanceLog>                                               $logs
     * @param callable|Closure(AttendanceLog $checkin , AttendanceLog $checkout):void $processFunction
     * @return Closure
     */
    protected function processAttendanceSlot(Collection $logs, callable|Closure $processFunction): Closure
    {
        return function (AttendanceLog $checkin, $index) use ($processFunction, $logs) {
            if (!$checkin->isCheckin()) {
                return true;
            }
            /** @var AttendanceLog $checkout */
            $checkout = $logs->get($index + 1);

            if (!$checkout?->isCheckout()) {
                return true;
            }

            return $processFunction($checkin, $checkout);
        };
    }
}
