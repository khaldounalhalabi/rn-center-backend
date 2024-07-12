<?php

namespace App\Repositories;

use App\Models\Follower;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Follower>
 */
class FollowerRepository extends BaseRepository
{
    protected string $modelClass = Follower::class;

    /**
     * @param array $relations
     * @param array $countable
     * @return Builder|Follower
     */
    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->whereHas('clinic', function (Builder $query) {
                $query->available();
            })->whereHas('customer', function (Builder $q) {
                $q->available();
            });
    }

    /**
     * @param $customerId
     * @param $clinicId
     * @return Follower|null
     */
    public function getByClinicAndCustomer($customerId, $clinicId): Follower|null
    {
        return $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function getByCustomer($customerId, array $relations = [], array $countable = []): ?array
    {
        $data = $this->globalQuery($relations, $countable)
            ->where('customer_id', $customerId)
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
