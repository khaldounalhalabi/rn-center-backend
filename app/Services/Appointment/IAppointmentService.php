<?php

namespace App\Services\Appointment;

use App\Services\Contracts\IBaseService;
use App\Models\Appointment;

/**
 * @extends IBaseService<Appointment>
 * Interface IUserService
 */
interface IAppointmentService extends IBaseService
{
    /**
     * @param $clinicId
     * @param array $relations
     * @param int $perPage
     * @return null|array
     */
    public function getClinicAppointments($clinicId, array $relations = [], int $perPage = 10): ?array;
}
