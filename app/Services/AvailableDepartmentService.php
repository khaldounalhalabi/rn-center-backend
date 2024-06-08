<?php

namespace App\Services;

use App\Models\AvailableDepartment;
use App\Repositories\AvailableDepartmentRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<AvailableDepartment>
 */
class AvailableDepartmentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AvailableDepartmentRepository::class;
}
