<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository
{
    protected string $modelClass = Service::class;

}
