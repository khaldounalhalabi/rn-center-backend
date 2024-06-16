<?php

namespace App\Repositories;

use App\Models\City;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<City>
 */
class CityRepository extends BaseRepository
{
    protected string $modelClass = City::class;
}
