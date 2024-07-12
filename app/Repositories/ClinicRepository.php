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

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered || !auth()->user(), function (Builder $query) {
                $query->available();
            });
    }

    public function byActiveSubscription($subscriptionId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations)
            ->withCount($countable)
            ->whereHas('activeSubscription', function (Builder $builder) use ($subscriptionId) {
                $builder->where('subscription_id', $subscriptionId);
            })
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data),
            ];
        } else {
            return null;
        }
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
}
