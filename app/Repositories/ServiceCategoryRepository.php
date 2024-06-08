<?php

namespace App\Repositories;

use App\Models\ServiceCategory;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<ServiceCategory>
 */
class ServiceCategoryRepository extends BaseRepository
{
    protected string $modelClass = ServiceCategory::class;

}
