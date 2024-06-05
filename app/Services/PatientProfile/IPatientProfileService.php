<?php

namespace App\Services\PatientProfile;

use App\Models\PatientProfile;
use App\Services\Contracts\IBaseService;

/**
 * @extends IBaseService<PatientProfile>
 * Interface IUserService
 */
interface IPatientProfileService extends IBaseService
{
    /**
     * @param       $customerId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return array{data , pagination_data}
     */
    public function getCustomerPatientProfiles($customerId, array $relations = [], array $countable = [], int $perPage = 10);
}
