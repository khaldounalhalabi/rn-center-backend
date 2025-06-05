<?php

namespace App\Services\v1\AttendanceLog;

use App\Enums\AttendanceLogStatusEnum;
use App\Enums\AttendanceLogTypeEnum;
use App\Enums\AttendanceStatusEnum;
use App\Models\AttendanceLog;
use App\Models\Schedule;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;
use Illuminate\Support\Collection;

/**
 * @extends BaseService<AttendanceLog>
 * @property AttendanceLogRepository $repository
 */
class AttendanceLogService extends BaseService
{
    use Makable;

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

        $this->repository->deleteByUser($attendance->id, $user->id);

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
}
