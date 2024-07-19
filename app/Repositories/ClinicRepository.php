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

    public function getOrderedByReviews(array $relations = [], array $countable = [])
    {
        $data = $this->globalQuery($relations, $countable, false);
    }

    public function getClinicsOrderedByFeatured(array $relations = [], array $countable = []): ?array
    {
        $data = $this->globalQuery($relations, $countable, false)
            ->select('*', DB::raw('(COALESCE(avg_reviews.avg_rate, 0) + COALESCE(followers_count, 0))/2 AS score'))
            ->leftJoin(DB::raw('(SELECT clinic_id, AVG(rate) AS avg_rate FROM reviews GROUP BY clinic_id) as avg_reviews'), 'clinics.id', '=', 'avg_reviews.clinic_id')
            ->leftJoin(DB::raw('(SELECT clinic_id, COUNT(customer_id) AS followers_count FROM followers GROUP BY clinic_id) as followers'), 'clinics.id', '=', 'followers.clinic_id')
            ->orderBy('score', 'desc')
            ->paginate($this->perPage);

        if ($data->count()) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }
        return null;
    }
}
