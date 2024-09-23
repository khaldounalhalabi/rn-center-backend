<?php

namespace App\Repositories;

use App\Models\SystemOffer;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<SystemOffer>
 */
class SystemOfferRepository extends BaseRepository
{
    protected string $modelClass = SystemOffer::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered, function (Builder|SystemOffer $query) {
                $query->active();
            });
    }

    public function getByIds(array $ids = [], ?int $clinicId = null, array $relations = [], array $countable = [])
    {
        return $this->globalQuery($relations, $countable)
            ->whereIn('id', $ids)
            ->active()
            ->when($clinicId,
                fn(Builder $query) => $query->whereHas('clinics',
                    function (Builder $q) use ($clinicId) {
                        $q->where('clinics.id', $clinicId);
                    }))->get();
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = [], ?int $perPage = 10): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->active()
                ->whereHas('clinics', function (Builder $query) use ($clinicId) {
                    $query->where('clinics.id', $clinicId);
                })
        );
    }
}
