<?php

namespace App\Services\Schedule;

use App\Enums\WeekDayEnum;
use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Services\Contracts\BaseService;
use Exception;

/**
 * @implements IScheduleService<Schedule>
 * @extends BaseService<Schedule>
 */
class ScheduleService extends BaseService implements IScheduleService
{
    /**
     * ScheduleService constructor.
     * @param ScheduleRepository $repository
     */
    public function __construct(ScheduleRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param int $clinicId
     * @return array{data:Schedule , appointment_gap:int}
     */
    public function getClinicSchedule(int $clinicId): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<Schedule> $data */
        $data = $this->repository->getSchedulesByType(Clinic::class, $clinicId);
        $appointmentGap = $data->pluck('appointment_gap')->unique()->first();
        return ['data' => $data, 'appointment_gap' => $appointmentGap];
    }

    /**
     * @param array $data
     * @param array $relationships
     * @return bool
     */
    public function storeUpdateSchedules(array $data, array $relationships = []): bool
    {
        if (isset($data['clinic_id'])) {
            $data['schedulable_id'] = $data['clinic_id'];
            $data['schedulable_type'] = Clinic::class;
        } elseif (isset($data['hospital_id'])) {
            $data['schedulable_id'] = $data['hospital_id'];
            $data['schedulable_type'] = Hospital::class;
        } else return false;

        $this->repository->deleteAll($data['schedulable_id'], $data['schedulable_type']);

        $schedules = collect();

        foreach ($data['schedules'] as $schedule) {
            $schedules->push([
                'day_of_week'      => $schedule['day_of_week'],
                'start_time'       => $schedule['start_time'],
                'end_time'         => $schedule['end_time'],
                'schedulable_id'   => $data['schedulable_id'],
                'schedulable_type' => $data['schedulable_type'],
                'appointment_gap'  => $data['appointment_gap'] ?? 10,
                'created_at'       => now()->format('Y-m-d H:i:s'),
                'updated_at'       => now()->format('Y-m-d H:i:s')
            ]);
        }

        return $this->repository->insert($schedules->unique()->toArray());
    }

    public function deleteAllClinicSchedules($clinicId): bool
    {
        try {
            $this->repository->deleteAll($clinicId, Clinic::class);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setDefaultClinicSchedule(Clinic|int $clinic)
    {
        $schedules = collect();
        if ($clinic instanceof Clinic) {
            $clinicId = $clinic->id;
        } else $clinicId = $clinic;

        foreach (WeekDayEnum::getAllValues() as $day) {
            $schedules->push([
                'day_of_week'      => $day,
                'start_time'       => "09:00",
                'end_time'         => "21:00",
                'schedulable_id'   => $clinicId,
                'schedulable_type' => Clinic::class,
                'appointment_gap'  => 10,
                'created_at'       => now()->format('Y-m-d H:i:s'),
                'updated_at'       => now()->format('Y-m-d H:i:s')
            ]);
        }

        return $this->repository->insert($schedules->unique()->toArray());
    }
}
