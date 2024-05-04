<?php

namespace  App\Repositories;

use App\Models\Medicine;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Medicine>
 */
class MedicineRepository extends BaseRepository
{
    public function __construct(Medicine $medicine)
    {
        parent::__construct($medicine);
    }
}
