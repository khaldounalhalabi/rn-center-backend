<?php

namespace App\Services;

use App\Enums\WeekDayEnum;
use App\Models\Clinic;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Services\Contracts\BaseService;
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
     * @param int $clinicId
     * @return Collection
     */
    public function getClinicSchedule(int $clinicId): Collection
    {
        /** @var Collection<Schedule> $data */
        $data = $this->repository->getByClinic($clinicId);
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
            $this->repository->deleteByClinic($data['clinic_id']);

            $schedules = collect();

            foreach ($data['schedules'] as $schedule) {
                $schedules->push([
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'clinic_id' => $data['clinic_id'],
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

    public function deleteAllClinicSchedules($clinicId): bool
    {
        try {
            $this->repository->deleteByClinic($clinicId);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function setDefaultClinicSchedule(Clinic|int $clinic): bool
    {
        $schedules = collect();
        if ($clinic instanceof Clinic) {
            $clinicId = $clinic->id;
        } else $clinicId = $clinic;

        foreach (WeekDayEnum::getAllValues() as $day) {
            $schedules->push([
                'day_of_week' => $day,
                'start_time' => "09:00",
                'end_time' => "21:00",
                'clinic_id' => $clinicId,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ]);
        }

        return $this->repository->insert($schedules->unique()->toArray());
    }
}
