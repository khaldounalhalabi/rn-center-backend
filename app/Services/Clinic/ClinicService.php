<?php

namespace App\Services\Clinic;

use App\Models\Clinic;
use App\Services\Contracts\BaseService;
use App\Repositories\ClinicRepository;

/**
 * @implements IClinicService<Clinic>
 * Class UserService
 */
class ClinicService extends BaseService implements IClinicService
{
    /**
     * ClinicService constructor.
     *
     * @param ClinicRepository $repository
     */
    public function __construct(ClinicRepository $repository)
    {
        parent::__construct($repository);
    }
}
