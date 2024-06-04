<?php

namespace App\Repositories;

use App\Models\MedicinePrescription;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<MedicinePrescription>
 */
class MedicinePrescriptionRepository extends BaseRepository
{
    public function __construct(MedicinePrescription $medicinePrescription)
    {
        parent::__construct($medicinePrescription);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return MedicinePrescription::insert($data);
    }
}
