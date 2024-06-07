<?php

namespace App\Repositories;

use App\Models\PatientProfile;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<PatientProfile>
 */
class PatientProfileRepository extends BaseRepository
{
    public function __construct(PatientProfile $patientProfile)
    {
        parent::__construct($patientProfile);
    }

    public function getByCustomerId($customerId, array $relations, array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->where('customer_id' , $customerId)
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
