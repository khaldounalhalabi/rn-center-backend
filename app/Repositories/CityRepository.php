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

    /**
     * @param array $relations
     * @return Builder
     */
    public function globalQuery(array $relations = []): Builder
    {
        $query = parent::globalQuery($relations);
        $query->when(!auth()->user()?->isAdmin(), function (Builder $query) {
            $query->isActive();
        });
        return $query;
    }
}
