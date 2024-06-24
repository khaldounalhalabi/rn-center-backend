<?php

namespace App\Repositories;

use App\Enums\HospitalStatusEnum;
use App\Models\Hospital;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Hospital>
 * @implements IBaseRepository<Hospital>
 */
class HospitalRepository extends BaseRepository
{
    protected string $modelClass = Hospital::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered, function (Builder $query) {
                $query->where('status', HospitalStatusEnum::ACTIVE->value);
            });
    }
}
