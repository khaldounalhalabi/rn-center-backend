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
     * @param array $countable
     * @return Builder
     */
    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        $query = parent::globalQuery($relations, $countable);
        $query->when(!auth()->user()?->isAdmin(), function (Builder $query) {
            $query->isActive();
        });
        return $query;
    }
}
