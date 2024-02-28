<?php

namespace  App\Repositories;

use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Clinic>
 * @implements IBaseRepository<Clinic>
 */
class ClinicRepository extends BaseRepository
{
    public function __construct(Clinic $clinic)
    {
        parent::__construct($clinic);
    }
}
