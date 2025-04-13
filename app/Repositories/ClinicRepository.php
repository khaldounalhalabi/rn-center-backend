<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Clinic>
 * <Clinic>
 */
class ClinicRepository extends BaseRepository
{
    protected string $modelClass = Clinic::class;

    public function getOnlineClinicsBySpeciality($specialityId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->online()
                ->whereHas('specialities', function (Builder $query) use ($specialityId) {
                    $query->where('specialities.id', $specialityId);
                })
        );
    }
}
