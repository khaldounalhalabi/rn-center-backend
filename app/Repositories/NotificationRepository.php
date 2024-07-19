<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class NotificationRepository extends BaseRepository
{
    protected string $modelClass = Notification::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->where('type', 'NOT LIKE', '%RealTime%');
    }

    public function getUserNotifications($notifiableId, $notifiableType = User::class, int $per_page = 10): ?array
    {
        $all = $this->notificationsBaseQuery($notifiableId, $notifiableType)
            ->paginate($this->perPage);

        if (count($all) > 0) {
            return ['data' => $all, 'pagination_data' => $this->formatPaginateData($all)];
        }

        return null;
    }

    private function notificationsBaseQuery($notifiableId, $notifiableType = User::class, bool $isAvailable = true)
    {
        return $this->globalQuery()
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
}
