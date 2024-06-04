<?php

namespace App\Services\Schedule;

use App\Models\Schedule;
use App\Services\Contracts\IBaseService;

/**
 * @extends IBaseService<Schedule>
 * Interface IUserService
 */
interface IScheduleService extends IBaseService
{
    /**
     * @param int $clinicId
     * @return array{data:Schedule , appointment_gap:int}
     */
    public function getClinicSchedule(int $clinicId): array;


    /**
     * @param array $data
     * @param array $relationships
     * @return bool
     */
    public function storeUpdateSchedules(array $data, array $relationships = []): bool;

    public function deleteAllClinicSchedules($clinicId): bool;
}
