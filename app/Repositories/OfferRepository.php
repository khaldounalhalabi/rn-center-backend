<?php

namespace App\Repositories;

use App\Models\Offer;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Offer>
 */
class OfferRepository extends BaseRepository
{
    protected string $modelClass = Offer::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        $query = parent::globalQuery($relations, $countable);

        return $query->when(auth()->user()?->isClinic(), function (Builder $query) {
            $query->where('clinic_id', auth()->user()?->getClinicId());
        });
    }

    public function getByids(array $ids = [], ?int $clinicId = null, array $relations = [], array $countable = [])
    {
        return $this->globalQuery($relations, $countable)
            ->whereIn('id', $ids)
            ->when($clinicId, fn(Builder $query) => $query->where('clinic_id', $clinicId))
            ->isActive()
            ->get();
    }
}
