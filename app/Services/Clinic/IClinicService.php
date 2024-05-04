<?php

namespace App\Services\Clinic;

use App\Services\Contracts\IBaseService;
use App\Models\Clinic;

/**
 * @extends IBaseService<Clinic>
 */
interface IClinicService extends IBaseService
{
    /**
     * @param $clinicId
     * @return array
     */
    public function getClinicAvailableTimes($clinicId): array;

    /**
     * @param $clinicId
     * @return string|null
     */
    public function toggleClinicStatus($clinicId): ?string;
}
