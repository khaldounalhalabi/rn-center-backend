<?php

namespace App\Modules\Notification\App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as LaravelDatabaseChannel;
use Illuminate\Notifications\Notification;

class DataBaseChannel extends LaravelDatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification): array
    {
        $data = $this->getData($notifiable, $notification);
        return [
            'id' => $notification->id,
            'type' => method_exists($notification, 'databaseType')
                ? $notification->databaseType()
                : get_class($notification),
            'data' => $data,
            'users' => json_encode($this->getUserIds($notifiable, $data), JSON_UNESCAPED_UNICODE),
            'read_at' => null,
            'model_id' => $data['model_id'] ?? null,
            'model_type' => $data['model_type'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'resource' => $data['resource'] ?? null,
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
