<?php

namespace  App\Repositories;

use App\Models\Prescription;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Prescription>
 */
class PrescriptionRepository extends BaseRepository
{
    public function __construct(Prescription $prescription)
    {
        parent::__construct($prescription);
    }
}
