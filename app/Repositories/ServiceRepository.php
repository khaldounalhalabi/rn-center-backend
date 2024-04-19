<?php

namespace  App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository
{
    public function __construct(Service $service)
    {
        parent::__construct($service);
    }
}
