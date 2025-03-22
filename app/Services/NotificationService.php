<?php

namespace App\Services;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @extends BaseService<Notification>
 * @property NotificationRepository $repository
 */
class NotificationService extends BaseService
{

    use Makable;

    protected string $repositoryClass = NotificationRepository::class;

    public function getUserNotifications(): ?array
    {
        return $this->repository->getUserNotifications(auth()->user()->id);
    }
}
