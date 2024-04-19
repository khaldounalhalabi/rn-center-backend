<?php

namespace App\Repositories;

use App\Models\ServiceCategory;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<ServiceCategory>
 */
class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(ServiceCategory $serviceCategory)
    {
        parent::__construct($serviceCategory);
    }
}
