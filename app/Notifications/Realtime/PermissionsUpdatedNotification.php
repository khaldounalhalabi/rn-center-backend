<?php

namespace App\Notifications\Realtime;

use App\Modules\Notification\App\Notifications\BaseNotification;
use NotificationChannels\Fcm\FcmChannel;

class PermissionsUpdatedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function via(mixed $notifiable): array
    {
        if ($notifiable->fcm_token) {
            return [FcmChannel::class];
        }

        return [];
    }
}
