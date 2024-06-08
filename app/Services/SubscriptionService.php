<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Subscription>
 * @property SubscriptionRepository $repository
 */
class SubscriptionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = SubscriptionRepository::class;
}
