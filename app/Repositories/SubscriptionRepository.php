<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Subscription>
 */
class SubscriptionRepository extends BaseRepository
{
    protected string $modelClass = Subscription::class;

}
