<?php

namespace App\Services\Hospital;

use App\Models\Hospital;
use App\Services\Contracts\BaseService;
use App\Repositories\HospitalRepository;

/**
 * @implements IHospitalService<Hospital>
 * Class UserService
 */
class HospitalService extends BaseService implements IHospitalService
{
    /**
     * HospitalService constructor.
     *
     * @param HospitalRepository $repository
     */
    public function __construct(HospitalRepository $repository)
    {
        parent::__construct($repository);
    }
}
