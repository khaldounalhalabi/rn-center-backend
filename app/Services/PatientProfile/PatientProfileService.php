<?php

namespace App\Services\PatientProfile;

use App\Models\PatientProfile;
use App\Repositories\PatientProfileRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IPatientProfileService<PatientProfile>
 * @extends BaseService<PatientProfile>
 */
class PatientProfileService extends BaseService implements IPatientProfileService
{
    /**
     * PatientProfileService constructor.
     * @param PatientProfileRepository $repository
     */
    public function __construct(PatientProfileRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param       $customerId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return null|array{data:mixed , pagination_data:array}
     */
    public function getCustomerPatientProfiles($customerId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getByCustomerId($customerId, $relations, $countable, $perPage);
    }
}
