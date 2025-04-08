<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Offer;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Offer>
 */
class OfferRepository extends BaseRepository
{
    protected string $modelClass = Offer::class;

    public function getByIds(array $ids = [], ?int $clinicId = null, array $relations = [], array $countable = [])
    {
        return $this->globalQuery($relations, $countable)
            ->whereIn('id', $ids)
            ->when($clinicId, fn(Builder $query) => $query->where('clinic_id', $clinicId))
            ->isActive()
            ->get();
    }

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        $query = parent::globalQuery($relations, $countable);

        return
            $query->when(auth()->user()?->isClinic(), function (Builder|Offer $builder) {
                $builder->where('clinic_id', auth()->user()?->getClinicId());
            })->when(!auth()->user() || auth()->user()?->isCustomer(), function (Builder|Offer $q) {
                $q->isActive()->whereHas('clinic', function (Builder|Clinic $b) {
                    $b->available();
                });
            })->when($this->filtered, function (Builder $builder) {
                $builder->isActive();
            });
    }

    public function getByClinicId($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('clinic_id', $clinicId)
                ->isActive()
        );
    }
}
