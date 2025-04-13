<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Medicine>
 */
class MedicineRepository extends BaseRepository
{
    protected string $modelClass = Medicine::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(
                isDoctor(),
                fn(Builder $query) => $query->where('clinic_id', clinic()?->id)
            );
    }
}
