<?php

namespace App\Notifications\Realtime;

use App\Models\User;
use App\Modules\Notification\App\Notifications\BaseNotification;
use NotificationChannels\Fcm\FcmChannel;

class AttendanceEditedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param mixed|User $notifiable
     * @return array|class-string[]
     */
    public function via(mixed $notifiable): array
    {
        if ($notifiable->fcmTokens->count()) {
            return [FcmChannel::class];
        }

        return [];
    }
}
