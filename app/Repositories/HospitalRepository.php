<?php

namespace App\Repositories;

use App\Models\Hospital;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Hospital>
 * @implements IBaseRepository<Hospital>
 */
class HospitalRepository extends BaseRepository
{
    public function __construct(Hospital $hospital)
    {
        parent::__construct($hospital);
    }
}
