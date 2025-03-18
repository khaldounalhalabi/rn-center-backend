<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @extends  BaseRepository<Clinic>
 * <Clinic>
 */
class ClinicRepository extends BaseRepository
{
    protected string $modelClass = Clinic::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered || !auth()->user()?->isAdmin(), function (Builder|Clinic $query) {
                $query->available();
            });
    }

    public function byActiveSubscription($subscriptionId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations)
                ->withCount($countable)
                ->whereHas('activeSubscription', function (Builder $builder) use ($subscriptionId) {
                    $builder->where('subscription_id', $subscriptionId);
                })
        );
    }

    public function getBySystemOffer($systemOfferId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->whereHas('systemOffers', function (Builder $query) use ($systemOfferId) {
                    $query->where('system_offers.id', $systemOfferId);
                })->available()
        );
    }

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
