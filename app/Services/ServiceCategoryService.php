<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Repositories\ServiceCategoryRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<ServiceCategory>
 * @property ServiceCategoryRepository $repository
 */
class ServiceCategoryService extends BaseService
{

    use Makable;

    protected string $repositoryClass = ServiceCategoryRepository::class;
}
