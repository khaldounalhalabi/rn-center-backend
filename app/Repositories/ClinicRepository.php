<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Clinic>
 * @implements IBaseRepository<Clinic>
 */
class ClinicRepository extends BaseRepository
{
    public function __construct(Clinic $clinic)
    {
        parent::__construct($clinic);
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
}
