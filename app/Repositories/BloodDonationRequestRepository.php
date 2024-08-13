<?php

namespace App\Repositories;

use App\Models\BloodDonationRequest;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<BloodDonationRequest>
 */
class BloodDonationRequestRepository extends BaseRepository
{
    protected string $modelClass = BloodDonationRequest::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable, $defaultOrder)
            ->when(!auth()?->user()?->isAdmin(), function (Builder|BloodDonationRequest $query) {
                $query->where('can_wait_until', '>', now()->format('Y-m-d H:i:s'));
            });
    }
}
