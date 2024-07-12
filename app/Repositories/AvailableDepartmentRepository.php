<?php

namespace App\Repositories;

use App\Models\AvailableDepartment;
use App\Repositories\Contracts\BaseRepository;


/**
 * @extends  BaseRepository<AvailableDepartment>
 */
class AvailableDepartmentRepository extends BaseRepository
{
    protected string $modelClass = AvailableDepartment::class;
}
