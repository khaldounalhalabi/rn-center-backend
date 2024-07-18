<?php

namespace App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as LaravelDatabaseChannel;
use Illuminate\Notifications\Notification;

class DataBaseChannel extends LaravelDatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification): array
    {
        $data = $this->getData($notifiable, $notification);
        return [
            'id'      => $notification->id,
            'type'    => str_replace("App\\Notifications\\", "",
                method_exists($notification, 'databaseType')
                    ? $notification->databaseType($notifiable)
                    : get_class($notification)),
            'data'    => $data,
            'users'   => json_encode($this->getUserIds($notifiable, $data), JSON_UNESCAPED_UNICODE),
            'read_at' => null,
        ];
    }

    private function getUserIds($notifiable = null, ?array $data = []): array
    {
        if (!$notifiable) return [];
        if (!$data) return [];

        $usersIds = collect([$notifiable->id]);
        if (isset($data['user_id'])) {
            $usersIds->push($data['user_id']);
        } elseif (isset($data['users'])) {
            $usersIds->push(...$data['users']);
        }

        return $usersIds->toArray();
    }
}
