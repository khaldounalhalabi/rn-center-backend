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

    public function getUnreadNotificationCounter(): int
    {
        return $this->repository->getUnreadNotificationCounter(auth()->user()->id);
    }

    public function markAllNotificationsAsRead(): int
    {
        return $this->repository->markAllNotificationsAsRead(auth()->user()->id);
    }

    public function markNotificationAsRead($id): bool
    {
        /** @var Notification|null $notification */
        $notification = $this->repository->find($id);
        Log::info(print_r($notification?->toArray(), 1));
        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        $notification->save();
        return true;
    }

    public function markLatestFiveAsRead(): bool
    {
        try {
            auth()->user()->unreadNotifications()->latest()->limit(5)->get()->markAsRead();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
