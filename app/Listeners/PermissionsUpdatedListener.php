<?php

namespace App\Listeners;

use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Realtime\PermissionsUpdatedNotification;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;

class PermissionsUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PermissionAttached|PermissionDetached $event): void
    {
        NotificationBuilder::make()
            ->to($event->model)
            ->method(NotifyMethod::ONE)
            ->notification(PermissionsUpdatedNotification::class)
            ->data([])
            ->send();
    }
}
