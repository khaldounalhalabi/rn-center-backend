<?php

namespace App\Services\PatientProfile;

use App\Models\PatientProfile;
use App\Services\Contracts\IBaseService;
use Ramsey\Collection\Collection;

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
     * @return null|array{data:Collection , pagination_data:array}
     */
    public function getCustomerPatientProfiles($customerId, array $relations = [], array $countable = [], int $perPage = 10): ?array;
}
