<?php

namespace App\Repositories;

use App\Models\ClinicEmployee;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<ClinicEmployee>
 */
class ClinicEmployeeRepository extends BaseRepository
{
    protected string $modelClass = ClinicEmployee::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(
                auth()->user()?->isClinic(),
                fn(Builder $query) => $query->where('clinic_id', auth()->user()?->getClinicId())
            );
    }
}
