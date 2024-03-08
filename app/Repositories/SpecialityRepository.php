<?php

namespace  App\Repositories;

use App\Models\Speciality;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Speciality>
 * @implements IBaseRepository<Speciality>
 */
class SpecialityRepository extends BaseRepository
{
    public function __construct(Speciality $speciality)
    {
        parent::__construct($speciality);
    }
}
