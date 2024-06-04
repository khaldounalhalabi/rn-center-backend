<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IServiceService<Service>
 * Class UserService
 */
class ServiceService extends BaseService implements IServiceService
{
    /**
     * ServiceService constructor.
     * @param ServiceRepository $repository
     */
    public function __construct(ServiceRepository $repository)
    {
        parent::__construct($repository);
    }
}
