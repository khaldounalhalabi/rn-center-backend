<?php

namespace App\Repositories;

use App\Models\Prescription;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Prescription>
 */
class PrescriptionRepository extends BaseRepository
{
    protected string $modelClass = Prescription::class;


    /**
     * @param int   $appointmentId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    public function getByAppointmentId(int $appointmentId, array $relations = [], int $perPage = 10): ?array
    {
        $all = $this->globalQuery($relations)->where('appointment_id', $appointmentId)
            ->paginate($perPage);

        if (count($all) > 0) {
            $pagination_data = $this->formatPaginateData($all);
            return ['data' => $all, 'pagination_data' => $pagination_data];
        }
        return null;
    }
}
