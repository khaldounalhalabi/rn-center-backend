<?php

namespace App\Services\Subscription;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements ISubscriptionService<Subscription>
 * @extends BaseService<Subscription>
 */
class SubscriptionService extends BaseService implements ISubscriptionService
{
    /**
     * SubscriptionService constructor.
     * @param SubscriptionRepository $repository
     */
    public function __construct(SubscriptionRepository $repository)
    {
        parent::__construct($repository);
    }
}
