<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Customer;
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
     * @param bool  $defaultOrder
     * @return Builder|Follower
     */
    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->whereHas('clinic', function (Builder|Clinic $query) {
                $query->available()->online();
            })->whereHas('customer', function (Builder|Customer $q) {
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
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
        );
    }
}
