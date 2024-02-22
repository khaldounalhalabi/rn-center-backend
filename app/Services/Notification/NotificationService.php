<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use App\Services\Contracts\BaseService;
use Illuminate\Support\Facades\Log;

/**
 * Class {Wishlist}Service
 */
class NotificationService extends BaseService implements INotificationService
{

    /**
     * WishlistService constructor.
     *
     * @param NotificationRepository $repository
     */
    public function __construct(NotificationRepository $repository)
    {
        parent::__construct($repository);
    }

    public function getUserNotifications()
    {
        return $this->repository->getUserNotifications(auth()->user()->id);
    }

    public function getUnreadNotificationCounter()
    {
        return $this->repository->getUnreadNotificationCounter(auth()->user()->id);
    }

    public function markAllNotificationsAsRead()
    {
        return $this->repository->markAllNotificationsAsRead(auth()->user()->id);
    }

    public function markNotificationAsRead($id): bool
    {
        /** @var Notification $notification */
        $notification = $this->repository->find($id);
        Log::info(print_r($notification->toArray() , 1));
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
            auth('web')->user()->unreadNotifications()->latest()->limit(5)->get()->markAsRead();
            return true;
        } catch (\Exception $e){
            return false;
        }
    }
}
