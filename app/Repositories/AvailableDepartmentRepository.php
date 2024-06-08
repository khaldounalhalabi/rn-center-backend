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
    protected string $modelClass = AvailableDepartment::class;
}
