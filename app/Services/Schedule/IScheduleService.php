<?php

namespace App\Services\Schedule;

use App\Services\Contracts\IBaseService;
use App\Models\Schedule;
use Illuminate\Support\Collection;

/**
 * @extends IBaseService<Schedule>
 * Interface IUserService
 */
interface IScheduleService extends IBaseService
{
    /**
     * @param int $clinicId
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getClinicSchedule(int $clinicId): Collection|array;


    /**
     * @param array $data
     * @param array $relationships
     * @return bool
     */
    public function storeUpdateSchedules(array $data, array $relationships = []): bool;

    public function deleteAllClinicSchedules($clinicId): bool;
}
