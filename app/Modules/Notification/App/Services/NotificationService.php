<?php

namespace App\Modules\Notification\App\Services;

use App\Enums\NotificationResourceEnum;
use App\Models\User;
use App\Modules\Notification\App\Models\Notification;
use App\Modules\SharedModule\Traits\Make;

class NotificationService
{
    use Make;

    public int $limit = 10;

    public function init(): void
    {
        $this->limit = request('per_page', 10);
    }

    public function myNotifications(): array|null
    {
        return $this->paginate(
            auth()->user()->notifications()
        );
    }

    public function show(string $id): Notification|null
    {
        $notification = auth()->user()->notifications()->where('notifications.id', $id)->first();
        if (!$notification) {
            return null;
        }

        $notification->markAsRead();
        return $notification;
    }

    public function unreadCount()
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAllAsRead()
    {
        return auth()->user()->unreadNotifications()->update([
            'read_at' => now(),
        ]);
    }

    public function markNotificationAsRead($id): bool
    {
        $notification = auth()->user()->notifications()->where('notifications.id', $id)->first();
        if (!$notification) {
            return false;
        }
        $notification->markAsRead();
        return true;
    }

    public function makeHandled(int $notifiableId, int $resourceId, NotificationResourceEnum $resource, string $notifiableType = User::class): bool|int
    {
        return Notification::where('notifiable_id', $notifiableId)
            ->where('notifiable_type', $notifiableType)
            ->where('resource_id', $resourceId)
            ->where('resource', $resource->value)
            ->update(['is_handled' => true]);
    }
}
