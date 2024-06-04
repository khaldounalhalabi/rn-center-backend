<?php

namespace App\Services\Prescription;

use App\Models\Prescription;
use App\Services\Contracts\IBaseService;

/**
 * @extends IBaseService<Prescription>
 * Interface IUserService
 */
interface IPrescriptionService extends IBaseService
{
    /**
     * @param int $medicineDataId
     * @return bool|null
     */
    public function removeMedicine(int $medicineDataId): ?bool;

    /**
     * @param int   $appointmentId
     * @param array $relations
     * @param int   $perPage
     * @return null|array{data:mixed , pagination_data:array}
     */
    public function getByAppointmentId(int $appointmentId, array $relations = [], int $perPage = 10): ?array;
}
