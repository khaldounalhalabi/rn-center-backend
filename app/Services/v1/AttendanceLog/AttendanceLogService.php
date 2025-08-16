<?php

namespace App\Services\v1\AttendanceLog;

use App\Enums\AttendanceLogStatusEnum;
use App\Enums\AttendanceLogTypeEnum;
use App\Enums\AttendanceStatusEnum;
use App\FormulaParser\SystemVariables\AttendanceVariables\AbsenceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\AttendanceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\ExpectedAttendanceDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\ExpectedAttendanceHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\OvertimeDaysCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\OvertimeHoursCount;
use App\FormulaParser\SystemVariables\AttendanceVariables\TotalAttendanceHoursCount;
use App\Models\AttendanceLog;
use App\Models\Schedule;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Realtime\AttendanceEditedNotification;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends BaseService<AttendanceLog>
 * @property AttendanceLogRepository $repository
 */
class AttendanceLogService extends BaseService
{
    use Makable;

    /**
     * statistics method cache duration in **SECONDS**
     */
    const CACHE_DURATION_FOR_STATISTICS = 60 * 60;

    protected string $repositoryClass = AttendanceLogRepository::class;

    public function editOrCreate($userId, array $data): ?Collection
    {
        $user = UserRepository::make()->find($userId);

        if (!$user) {
            return null;
        }

        $attendance = AttendanceRepository::make()->getByDateOrCreate($data['attendance_at']);

        AttendanceRepository::make()->update([
            'status' => AttendanceStatusEnum::DRAFT->value
        ], $attendance);

        $this->repository->deleteByAttendanceAndUser($attendance->id, $user->id);

        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to($user)
            ->method(NotifyMethod::ONE)
            ->send();

        $this->invalidateAttendanceStatisticsCache($user->id);

        if (!isset($data['attendance_shifts']) || (count($data['attendance_shifts']) == 0)) {
            return collect();
        }

        if ($user->isDoctor()) {
            $scheduleSlots = $user->clinic->schedules()->where('day_of_week', strtolower($attendance->date->dayName))->get();
        } else {
            $scheduleSlots = $user->schedules()->where('day_of_week', strtolower($attendance->date->dayName))->get();
        }

        /** @var (array{attend_from:Carbon , attend_to:Carbon})[] $attendanceShifts */
        $attendanceShifts = $this->removeOverlappedSlots($data['attendance_shifts']);
        $attendanceShifts = collect($attendanceShifts)
            ->flatMap(/*** @param array{attend_from:Carbon , attend_to:Carbon} $attendanceShift */
                function (array $attendanceShift) use ($scheduleSlots, $attendance, $user) {
                    $checkinTime = Carbon::parse($attendance->date?->format('Y-m-d') . ' ' . $attendanceShift['attend_from']->format('H:i:s'));
                    $type = AttendanceLogTypeEnum::CHECKIN->value;
                    $result[0] = [
                        'user_id' => $user->id,
                        'attendance_id' => $attendance->id,
                        'status' => $this->getLogStatus($checkinTime, $type, $scheduleSlots),
                        'type' => $type,
                        'attend_at' => $checkinTime,
                    ];

                    if (isset($attendanceShift['attend_to'])) {
                        $checkoutTime = Carbon::parse($attendance->date?->format('Y-m-d') . ' ' . $attendanceShift['attend_to']->format('H:i:s'));
                        $type = AttendanceLogTypeEnum::CHECKOUT->value;
                        $result[1] = [
                            'user_id' => $user->id,
                            'attendance_id' => $attendance->id,
                            'status' => $this->getLogStatus($checkoutTime, $type, $scheduleSlots),
                            'type' => $type,
                            'attend_at' => $checkoutTime,
                        ];
                    }

                    return $result;
                });

        AttendanceLogRepository::make()->insert($attendanceShifts->toArray());

        return $attendanceShifts
            ->map(fn($attendanceShift) => [...$attendanceShift, 'attend_at' => $attendanceShift['attend_at']->format('Y-m-d H:i:s')]);
    }

    /**
     * @param array $attendanceShifts
     * @return array
     */
    private function removeOverlappedSlots(array $attendanceShifts): array
    {
        $attendanceShifts = collect($attendanceShifts)
            ->map(fn($attendanceShift) => [
                'attend_from' => Carbon::parse($attendanceShift['attend_from']),
                'attend_to' => isset($attendanceShift['attend_to']) ? Carbon::parse($attendanceShift['attend_to']) : null,
            ])->sortBy('attend_from')
            ->values()
            ->toArray();

        $isDirty = false;

        for ($i = 0; $i < count($attendanceShifts); $i++) {
            /** @var array{attend_from:Carbon , attend_to:Carbon|null} $shift */
            $shift = $attendanceShifts[$i] ?? null;

            if (!$shift) {
                continue;
            }

            if (!isset($shift['attend_to'])) {
                continue;
            }

            for ($j = 0; $j < count($attendanceShifts); $j++) {
                /** @var array{attend_from:Carbon , attend_to:Carbon|null} $innerShift */
                $innerShift = $attendanceShifts[$j] ?? null;

                if (!$innerShift) {
                    continue;
                }

                if ($i == $j) {
                    continue;
                }

                if (
                    $shift['attend_from']->greaterThanOrEqualTo($innerShift['attend_from'])
                    && $shift['attend_from']->lessThanOrEqualTo($innerShift['attend_to'])
                    && $shift['attend_to']->greaterThanOrEqualTo($innerShift['attend_from'])
                    && $shift['attend_to']->lessThanOrEqualTo($innerShift['attend_to'])
                ) {
                    $isDirty = true;
                    unset($attendanceShifts[$i]);
                } elseif (
                    $shift['attend_from']->greaterThanOrEqualTo($innerShift['attend_from'])
                    && $shift['attend_from']->lessThanOrEqualTo($innerShift['attend_to'])
                ) {
                    if ($shift['attend_to']->greaterThanOrEqualTo($innerShift['attend_to'])) {
                        $attendanceShifts[$j]['attend_to'] = $shift['attend_to'];
                    }
                    $isDirty = true;
                    unset($attendanceShifts[$i]);
                } elseif ($shift['attend_to']->greaterThanOrEqualTo($innerShift['attend_from'])
                    && $shift['attend_to']->lessThanOrEqualTo($innerShift['attend_to'])) {
                    if ($shift['attend_from']->lessThanOrEqualTo($innerShift['attend_from'])) {
                        $attendanceShifts[$j]['attend_from'] = $shift['attend_from'];
                    }
                    $isDirty = true;
                    unset($attendanceShifts[$i]);
                }
            }
        }

        if (!$isDirty) {
            return $attendanceShifts;
        } else {
            return $this->removeOverlappedSlots($attendanceShifts);
        }
    }

    /**
     * Determines the attendance log status based on schedule and attendance time
     * @param Carbon                    $attendAt
     * @param string                    $type
     * @param Collection<Schedule>|null $scheduleSlots
     * @return string
     */
    public function getLogStatus(Carbon $attendAt, string $type, ?Collection $scheduleSlots = null): string
    {
        // Configurable grace periods in minutes
        $checkinGracePeriodMinutes = config('attendance.checkin_grace_period', 5);
        $checkoutGracePeriodMinutes = config('attendance.checkout_grace_period', 5);

        if (!$scheduleSlots) {
            return AttendanceLogStatusEnum::OVER_TIME->value;
        }
        if ($scheduleSlots->isEmpty()) {
            return AttendanceLogStatusEnum::OVER_TIME->value;
        }

        $day = strtolower($attendAt->dayName);

        // Filter schedule slots for the current day
        $daySchedules = $scheduleSlots->filter(function ($schedule) use ($day) {
            return strtolower($schedule->day_of_week) === $day;
        });

        if ($daySchedules->isEmpty()) {
            return AttendanceLogStatusEnum::OVER_TIME->value;
        }

        $attendTime = $attendAt;

        if ($type === AttendanceLogTypeEnum::CHECKIN->value) {
            // For check-in, compare with start times considering a grace period
            $onTimeSchedule = $daySchedules->first(function ($schedule) use ($attendTime, $checkinGracePeriodMinutes) {
                $scheduleStartTime = Carbon::parse($schedule->start_time);
                $graceEndTime = (clone $scheduleStartTime)->addMinutes($checkinGracePeriodMinutes);

                // Check if attendance time is within the allowed start time range and grace period
                return $attendTime <= $graceEndTime && $attendTime >= $scheduleStartTime;
            });

            if ($onTimeSchedule) {
                return AttendanceLogStatusEnum::ON_TIME->value;
            }

            // If there's no on-time schedule, then the user is late
            $lateSchedule = $daySchedules->first(function ($schedule) use ($attendTime, $checkinGracePeriodMinutes) {
                $scheduleStartTime = Carbon::parse($schedule->start_time);
                $graceEndTime = (clone $scheduleStartTime)->addMinutes($checkinGracePeriodMinutes);

                // Check if attendance time is after the grace period but before end time
                return $attendTime > $graceEndTime && $attendTime < $schedule->end_time;
            });

            if ($lateSchedule) {
                return AttendanceLogStatusEnum::LATE->value;
            }

            return AttendanceLogStatusEnum::OVER_TIME->value;
        } else if ($type === AttendanceLogTypeEnum::CHECKOUT->value) {
            // For check-out, compare with end times
            $earlyLeaveSchedule = $daySchedules->first(function ($schedule) use ($attendTime, $checkoutGracePeriodMinutes) {
                $scheduleEndTime = Carbon::parse($schedule->end_time);
                $graceStartTime = (clone $scheduleEndTime)->subMinutes($checkoutGracePeriodMinutes);

                // Check if attendance time is before the expected end time minus grace period
                return $attendTime < $graceStartTime && $attendTime < $scheduleEndTime;
            });

            if ($earlyLeaveSchedule) {
                return AttendanceLogStatusEnum::EARLY_LEAVE->value;
            }

            $onTimeSchedule = $daySchedules->first(function ($schedule) use ($attendTime, $checkoutGracePeriodMinutes) {
                $scheduleEndTime = Carbon::parse($schedule->end_time);
                $graceStartTime = (clone $scheduleEndTime)->subMinutes($checkoutGracePeriodMinutes);

                // Check if attendance time is within the acceptable range around the end time
                return $attendTime >= $graceStartTime && $attendTime <= $schedule->end_time;
            });

            if ($onTimeSchedule) {
                return AttendanceLogStatusEnum::ON_TIME->value;
            }

            // If the user checks out after the end time of any schedule slot
            $overtimeSchedule = $daySchedules->first(function ($schedule) use ($checkoutGracePeriodMinutes, $attendTime) {
                $scheduleEndTime = $schedule->end_time;
                $graceEndtime = (clone $scheduleEndTime)->addMinutes($checkoutGracePeriodMinutes);
                // Check if attendance time is after the expected end time
                return $attendTime > $scheduleEndTime && $attendTime > $graceEndtime;
            });

            if ($overtimeSchedule) {
                return AttendanceLogStatusEnum::OVER_TIME->value;
            }

            return AttendanceLogStatusEnum::ON_TIME->value;
        }

        // Default case if the type is not recognized
        return AttendanceLogStatusEnum::OVER_TIME->value;
    }

    /**
     * @param string|null $year
     * @param string|null $month
     * @return CollectionAlias<AttendanceLog>
     */
    public function myAttendanceLogs(?string $year = null, ?string $month = null): Collection
    {
        return $this->repository->getByUserAndYearAndMonth(
            user()?->id,
            $year ?? now()->year,
            $month ?? now()->month,
        );
    }

    /**
     * @param array $relations
     * @param array $countable
     * @return AttendanceLog|null
     */
    public function checkin(array $relations = [], array $countable = []): ?AttendanceLog
    {
        $scheduleInDay = user()?->schedules->groupBy('day_of_week')->get(strtolower(now()->dayName)) ?? collect();
        $latestLog = $this->repository->getLatestLogInDay(now()->format('Y-m-d'), user()?->id);
        $attendance = AttendanceRepository::make()->getByDateOrCreate(now());

        if ($latestLog && $latestLog?->isCheckin()) {
            $this->repository->create([
                'attendance_id' => $attendance->id,
                'attend_at' => now()->subMinute(),
                'type' => AttendanceLogTypeEnum::CHECKOUT->value,
                'user_id' => user()?->id,
                'status' => $this->getLogStatus(now()->subMinute(), AttendanceLogTypeEnum::CHECKOUT->value, $scheduleInDay),
            ]);
        } elseif ($latestLog && $latestLog?->isCheckout() && $latestLog->attend_at->greaterThanOrEqualTo(now())) {
            return null;
        }

        return $this->repository->create([
            'attendance_id' => $attendance->id,
            'attend_at' => now(),
            'type' => AttendanceLogTypeEnum::CHECKIN->value,
            'user_id' => user()?->id,
            'status' => $this->getLogStatus(now(), AttendanceLogTypeEnum::CHECKIN->value, $scheduleInDay),
        ], $relations, $countable);
    }

    public function checkout(array $relations = [], array $countable = []): ?AttendanceLog
    {
        $scheduleInDay = user()?->schedules->groupBy('day_of_week')->get(strtolower(now()->dayName)) ?? collect();
        $latestLog = $this->repository->getLatestLogInDay(now()->format('Y-m-d'), user()?->id);
        $attendance = AttendanceRepository::make()->getByDateOrCreate(now());
        $this->invalidateAttendanceStatisticsCache(user()->id);

        if ($latestLog && $latestLog?->isCheckout()) {
            $this->repository->create([
                'attendance_id' => $attendance->id,
                'attend_at' => now()->subMinute(),
                'type' => AttendanceLogTypeEnum::CHECKIN->value,
                'user_id' => user()?->id,
                'status' => $this->getLogStatus(now()->subMinute(), AttendanceLogTypeEnum::CHECKIN->value, $scheduleInDay),
            ]);
        } elseif ($latestLog && $latestLog?->isCheckin() && $latestLog->attend_at->greaterThanOrEqualTo(now())) {
            return null;
        }

        return $this->repository->create([
            'attendance_id' => $attendance->id,
            'attend_at' => now(),
            'type' => AttendanceLogTypeEnum::CHECKOUT->value,
            'user_id' => user()?->id,
            'status' => $this->getLogStatus(now(), AttendanceLogTypeEnum::CHECKOUT->value, $scheduleInDay),
        ], $relations, $countable);
    }

    public function latestLogToday(array $relations = [], array $countable = []): ?AttendanceLog
    {
        return $this->repository->getLatestLogInDay(
            now()->format('Y-m-d'),
            user()?->id,
            $relations,
            $countable
        );
    }

    private function getStatisticsCacheKey(int $userId): string
    {
        $startOfMonth = now()->firstOfMonth();
        $endOfMonth = now()->lastOfMonth();

        return "attendance_statistics=>user_id:{$userId}=>from:{$startOfMonth->format('Y-m-d')}=>to:{$endOfMonth->format('Y-m-d')}";
    }

    public function attendanceStatisticsByUser(int $userId): array
    {
        $startOfMonth = now()->firstOfMonth();
        $endOfMonth = now()->lastOfMonth();
        $cacheKey = $this->getStatisticsCacheKey($userId);
        $user = UserRepository::make()->find($userId);

        if (!$user) {
            return [
                'absence_days' => 0,
                'attendance_days' => 0,
                'attendance_hours' => 0,
                'expected_hours' => 0,
                'expected_days' => 0,
                'attendance_hours_in_day' => 0,
            ];
        }

        return cache()
            ->remember(
                $cacheKey,
                self::CACHE_DURATION_FOR_STATISTICS,
                function () use ($user, $userId, $startOfMonth, $endOfMonth) {
                    $logs = $this->repository->getInRange($userId, $startOfMonth, $endOfMonth);
                    $logsInDay = $this->repository->getInRange($userId, now()->startOfDay(), now()->endOfDay());

                    $absenceDays = (new AbsenceDaysCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $attendanceDays = (new AttendanceDaysCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $attendanceHours = (new TotalAttendanceHoursCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $expectedHours = (new ExpectedAttendanceHoursCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $expectedDays = (new ExpectedAttendanceDaysCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $attendanceInDay = (new TotalAttendanceHoursCount($user, $logsInDay, now()->startOfDay(), now()->endOfDay()))->getResult();
                    $overtimeHours = (new OvertimeHoursCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();
                    $overtimeDays = (new OvertimeDaysCount($user, $logs, $startOfMonth, $endOfMonth))->getResult();

                    return [
                        'absence_days' => $absenceDays,
                        'attendance_days' => $attendanceDays,
                        'attendance_hours' => $attendanceHours,
                        'expected_hours' => $expectedHours,
                        'expected_days' => $expectedDays,
                        'attendance_hours_in_day' => $attendanceInDay,
                        'overtime_hours' => $overtimeHours,
                        'overtime_days' => $overtimeDays,
                    ];
                }
            );
    }

    public function invalidateAttendanceStatisticsCache(int $userId): void
    {
        $cacheKey = $this->getStatisticsCacheKey($userId);
        cache()->delete($cacheKey);
    }

    public function exportMine(): BinaryFileResponse
    {
        return $this->repository->exportByUser(user()->id);
    }
}
