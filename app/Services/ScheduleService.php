<?php

namespace App\Services;

use App\Enums\WeekDayEnum;
use App\Models\Clinic;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ClinicRepository;
use App\Repositories\ScheduleRepository;
use App\Services\Contracts\BaseService;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use App\Traits\Makable;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @extends BaseService<Schedule>
 * @property ScheduleRepository $repository
 */
class ScheduleService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ScheduleRepository::class;

    /**
     * @param int                       $scheduleableId
     * @param class-string<Clinic|User> $type
     * @return Collection
     */
    public function getByScheduleable(int $scheduleableId, string $type = Clinic::class): Collection
    {
        /** @var Collection<Schedule> $data */
        $data = $this->repository->getByScheduleable($scheduleableId, $type);
        return $data;
    }

    /**
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    public function storeUpdateSchedules(array $data): bool
    {
        DB::beginTransaction();
        try {
            if (isset($data['clinic_id'])) {
                $this->repository->deleteByScheduleable($data['clinic_id']);
                $scheduleableId = $data['clinic_id'];
                $type = Clinic::class;
                $clinic = ClinicRepository::make()->find($data['clinic_id']);
                AttendanceLogService::make()->invalidateAttendanceStatisticsCache($clinic->user_id);
            } else {
                $scheduleableId = $data['user_id'];
                $type = User::class;
                $this->repository->deleteByScheduleable($data['user_id'], $type);
                AttendanceLogService::make()->invalidateAttendanceStatisticsCache($data['user_id']);
            }

            $schedules = collect();

            foreach ($data['schedules'] as $schedule) {
                $schedules->push([
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'scheduleable_id' => $scheduleableId,
                    'scheduleable_type' => $type,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s')
                ]);
            }
            $this->repository->insert($schedules->unique()->toArray());
            DB::commit();
            return true;
        } catch (Exception) {
            DB::rollBack();
            return false;
        }
    }

    public function deleteAllSchedules(int $scheduleableId, string $type = Clinic::class): bool
    {
        try {
            $this->repository->deleteByScheduleable($scheduleableId, $type);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function setDefaultSchedule(Clinic|User $scheduleable): bool
    {
        $schedules = collect();

        foreach (WeekDayEnum::getAllValues() as $day) {
            $schedules->push([
                'day_of_week' => $day,
                'start_time' => "09:00",
                'end_time' => "21:00",
                'scheduleable_id' => $scheduleable->id,
                'scheduleable_type' => get_class($scheduleable),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ]);
        }

        return $this->repository->insert($schedules->unique()->toArray());
    }
}
