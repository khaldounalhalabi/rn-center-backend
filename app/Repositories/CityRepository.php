<?php

namespace App\Repositories;

use App\Models\City;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<City>
 */
class CityRepository extends BaseRepository
{
    protected string $modelClass = City::class;

}
