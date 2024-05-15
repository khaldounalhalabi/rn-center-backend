<?php

namespace App\Services\Speciality;

use App\Models\Speciality;
use App\Services\Contracts\BaseService;
use App\Repositories\SpecialityRepository;

/**
 * @implements ISpecialityService<Speciality>
 * Class UserService
 */
class SpecialityService extends BaseService implements ISpecialityService
{
    /**
     * SpecialityService constructor.
     * @param SpecialityRepository $repository
     */
    public function __construct(SpecialityRepository $repository)
    {
        parent::__construct($repository);
    }
}
