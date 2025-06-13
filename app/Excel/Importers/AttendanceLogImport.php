<?php

namespace App\Excel\Importers;

use App\Enums\AttendanceLogTypeEnum;
use App\Excel\BaseImporter;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\UserRepository;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AttendanceLogImport extends BaseImporter implements ToCollection
{
    public function collection(Collection $collection): void
    {
        $attendances = [];
        $data = [];
        $collection
            ->map(fn($item) => [
                'attend_at' => $this->processRow('attend_at', $item['attend_at']),
                'user_id' => $this->processRow('user_id', $item['user_id']),
            ])->groupBy('user_id')
            ->each(function (Collection $logs, $userId) use (&$attendances, &$data) {
                $user = UserRepository::make()->find($userId);
                if (!$user) {
                    return true;
                }

                $logs->sortBy('attend_at')
                    ->unique(fn($log) => Carbon::parse($log['attend_at'])->format('Y-m-d H:i:s'))
                    ->groupBy(fn($log) => Carbon::parse($log['attend_at'])->format('Y-m-d'))
                    ->each(function (Collection $logs, string $date) use (&$attendances, $user, &$data) {
                        AttendanceLogRepository::make()->deleteByDateAndUser($user->id, $date);
                        $type = AttendanceLogTypeEnum::CHECKIN->value;
                        $dayName = strtolower(Carbon::parse($date)->dayName);
                        if ($user->isDoctor() && $user->clinic) {
                            $scheduleSlots = $user->clinic?->schedules()->where('day_of_week', $dayName)->get();
                        } else {
                            $scheduleSlots = $user->schedules()->where('day_of_week', $dayName)->get();
                        }
                        $logs->sortBy(fn($log) => $log['attend_at'])
                            ->each(function ($log) use ($scheduleSlots, $date, &$attendances, $user, &$type, &$data) {
                                if (isset($attendances[$date])) {
                                    $attendance = $attendances[$date];
                                } else {
                                    $attendance = AttendanceRepository::make()->getByDateOrCreate($date);
                                    $attendances[$date] = $attendance;
                                }

                                $data[] = [
                                    'attend_at' => $log['attend_at'],
                                    'type' => $type,
                                    'status' => AttendanceLogService::make()->getLogStatus(Carbon::parse($log['attend_at']), $type, $scheduleSlots),
                                    'attendance_id' => $attendance->id,
                                    'user_id' => $user->id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];

                                $type = $type == AttendanceLogTypeEnum::CHECKIN->value
                                    ? AttendanceLogTypeEnum::CHECKOUT->value
                                    : AttendanceLogTypeEnum::CHECKIN->value;
                            });
                    });
                return true;
            });

        AttendanceLogRepository::make()->insert($data);
    }
}
