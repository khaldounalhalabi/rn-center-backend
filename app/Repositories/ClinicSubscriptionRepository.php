<?php

namespace App\Repositories;

use App\Models\ClinicSubscription;
use App\Repositories\Contracts\BaseRepository;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends  BaseRepository<ClinicSubscription>
 */
class ClinicSubscriptionRepository extends BaseRepository
{
    public function __construct(ClinicSubscription $clinicSubscription)
    {
        parent::__construct($clinicSubscription);
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    #[ArrayShape(['data' => "mixed", 'pagination_data' => "array"])]
    public function getByClinic($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;

        $data = $this->globalQuery($relations)
            ->where('clinic_id', $clinicId)
            ->paginate($perPage);

        if (count($data)) {
            return [
                'data' => $data,
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
