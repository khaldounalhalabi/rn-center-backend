<?php

namespace App\Services\ServiceCategory;

use App\Models\ServiceCategory;
use App\Services\Contracts\BaseService;
use App\Repositories\ServiceCategoryRepository;

/**
 * @implements IServiceCategoryService<ServiceCategory>
 * Class UserService
 */
class ServiceCategoryService extends BaseService implements IServiceCategoryService
{
    /**
     * ServiceCategoryService constructor.
     * @param ServiceCategoryRepository $repository
     */
    public function __construct(ServiceCategoryRepository $repository)
    {
        parent::__construct($repository);
    }
}
