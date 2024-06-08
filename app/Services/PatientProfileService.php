<?php

namespace App\Services;

use App\Models\PatientProfile;
use App\Repositories\PatientProfileRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<PatientProfile>
 * @property PatientProfileRepository $repository
 */
class PatientProfileService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PatientProfileRepository::class;

    /**
     * @param       $customerId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return array|null
     */
    public function getCustomerPatientProfiles($customerId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getByCustomerId($customerId, $relations, $countable, $perPage);
    }
}
