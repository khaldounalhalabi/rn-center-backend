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

    /**
     * @param mixed|User $notifiable
     * @return string[]
     */
    public static function baseNotificationVia(mixed $notifiable): array
    {
        if ($notifiable->fcmTokens()->count()) {
            return [FcmChannel::class, 'database'];
        }

        return ['database'];
    }
}
