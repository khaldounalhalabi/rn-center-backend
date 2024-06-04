<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Subscription>
 */
class SubscriptionRepository extends BaseRepository
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
    }
}
