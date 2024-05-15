<?php

namespace App\Services\AvailableDepartment;

use App\Models\AvailableDepartment;
use App\Services\Contracts\BaseService;
use App\Repositories\AvailableDepartmentRepository;

/**
 * @implements IAvailableDepartmentService<AvailableDepartment>
 * Class UserService
 */
class AvailableDepartmentService extends BaseService implements IAvailableDepartmentService
{
    /**
     * AvailableDepartmentService constructor.
     * @param AvailableDepartmentRepository $repository
     */
    public function __construct(AvailableDepartmentRepository $repository)
    {
        parent::__construct($repository);
    }
}
