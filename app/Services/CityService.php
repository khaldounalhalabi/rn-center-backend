<?php

namespace App\Services;

use App\Models\City;
use App\Repositories\CityRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<City>
 * @property CityRepository $repository
 */
class CityService extends BaseService
{
    use Makable;

    protected string $repositoryClass = CityRepository::class;
}
