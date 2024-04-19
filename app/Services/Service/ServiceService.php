<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Services\Contracts\BaseService;
use App\Repositories\ServiceRepository;

/**
 * @implements IServiceService<Service>
 * Class UserService
 */
class ServiceService extends BaseService implements IServiceService
{
    /**
     * ServiceService constructor.
     *
     * @param ServiceRepository $repository
     */
    public function __construct(ServiceRepository $repository)
    {
        parent::__construct($repository);
    }
}
