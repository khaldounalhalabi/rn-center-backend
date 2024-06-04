<?php

namespace App\Repositories;

use App\Models\City;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<City>
 */
class CityRepository extends BaseRepository
{
    public function __construct(City $city)
    {
        parent::__construct($city);
    }
}
