<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;

class NotificationRepository extends BaseRepository
{
    protected string $modelClass = Notification::class;


    public function getUserNotifications($notifiableId, $notifiableType = User::class, int $per_page = 10): ?array
    {
        $all = $this->notificationsBaseQuery($notifiableId, $notifiableType)
            ->paginate($per_page);

        if (count($all) > 0) {
            $pagination_data = $this->formatPaginateData($all);
            return ['data' => $all, 'pagination_data' => $pagination_data];
        }

        return null;
    }

    private function notificationsBaseQuery($notifiableId, $notifiableType = User::class, bool $isAvailable = true)
    {
        return Notification::query()
            ->when($isAvailable, fn($q) => $q->available())
            ->where('notifiable_id', $notifiableId)
            ->where('notifiable_type', $notifiableType);
    }

    public function getUnreadNotificationCounter($notifiableId, $notifiableType = User::class): int
    {
        return $this->notificationsBaseQuery($notifiableId, $notifiableType)
            ->where('read_at', null)
            ->count();
    }

    public function markAllNotificationsAsRead($notifiableId, $notifiableType = User::class): int
    {
        return $this->notificationsBaseQuery($notifiableId, $notifiableType)
            ->whereNull('read_at')
            ->update(['read_at' => now()->format('Y-m-d H:i:s')]);
    }

    public function disableUserNotifications($userId): void
    {
        if (!app()->environment('testing')) {
            Notification::where('notifiable_id', $userId)
                ->orWhereJsonContains('users', $userId)
                ->update([
                    'is_available' => false
                ]);
        }
    }
}
