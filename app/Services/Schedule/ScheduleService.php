<?php

namespace App\Services\Schedule;

use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Services\Contracts\BaseService;
use Illuminate\Support\Collection;

/**
 * @implements IScheduleService<Schedule>
 * Class UserService
 */
class ScheduleService extends BaseService implements IScheduleService
{
    /**
     * ScheduleService constructor.
     *
     * @param ScheduleRepository $repository
     */
    public function __construct(ScheduleRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param int $clinicId
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getClinicSchedule(int $clinicId): Collection|array
    {
        return $this->repository->getSchedulesByType(Clinic::class, $clinicId);
    }

    /**
     * @param array $data
     * @param array $relationships
     * @return Schedule|null
     */
    public function store(array $data, array $relationships = []): ?Schedule
    {
        if (isset($data['clinic_id'])) {
            $data['schedulable_id'] = $data['clinic_id'];
            $data['schedulable_type'] = Clinic::class;
        } elseif (isset($data['hospital_id'])) {
            $data['schedulable_id'] = $data['hospital_id'];
            $data['schedulable_type'] = Hospital::class;
        } else return null;

        return $this->repository->create($data, $relationships);
    }
}
