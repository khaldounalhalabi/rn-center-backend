<?php

namespace App\Services\City;

use App\Models\City;
use App\Repositories\CityRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements ICityService<City>
 * Class UserService
 */
class CityService extends BaseService implements ICityService
{
    /**
     * CityService constructor.
     * @param CityRepository $repository
     */
    public function __construct(CityRepository $repository)
    {
        parent::__construct($repository);
    }
}
