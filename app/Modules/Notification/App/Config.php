<?php

namespace App\Modules\Notification\App;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use NotificationChannels\Fcm\FcmChannel;

class Config
{
    public static function userQuery(): Builder
    {
        return User::query();
    }

    public static function notifiableClass(): string
    {
        return User::class;
    }

    public static function baseNotificationVia(mixed $notifiable): array
    {
        if ($notifiable->fcm_token) {
            return [FcmChannel::class, 'database'];
        }

        return ['database'];
    }
}
