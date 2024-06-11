<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository
{
    protected string $modelClass = Service::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        $query = parent::globalQuery($relations, $countable);

        return $query->when(auth()->user()?->isDoctor(), function (Builder $query) {
            $query->where('clinic_id', auth()->user()?->clinic?->id);
        });
    }
}
