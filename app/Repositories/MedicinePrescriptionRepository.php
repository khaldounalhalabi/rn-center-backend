<?php

namespace App\Repositories;

use App\Models\MedicinePrescription;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<MedicinePrescription>
 */
class MedicinePrescriptionRepository extends BaseRepository
{
    protected string $modelClass = MedicinePrescription::class;


    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return MedicinePrescription::insert($data);
    }
}
