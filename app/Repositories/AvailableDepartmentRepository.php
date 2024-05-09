<?php

namespace App\Repositories;

use App\Models\AvailableDepartment;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<AvailableDepartment>
 * @implements IBaseRepository<AvailableDepartment>
 */
class AvailableDepartmentRepository extends BaseRepository
{
    public function __construct(AvailableDepartment $availableDepartment)
    {
        parent::__construct($availableDepartment);
    }
}
