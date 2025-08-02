<?php

namespace App\Observers;

use App\Enums\RolesPermissionEnum;
use App\Models\AttendanceLog;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Realtime\AttendanceEditedNotification;

class AttendanceLogObserver
{
    /**
     * Handle the AttendanceLog "created" event.
     */
    public function created(AttendanceLog $attendanceLog): void
    {
        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to(RolesPermissionEnum::ADMIN['role'])
            ->method(NotifyMethod::BY_ROLE)
            ->send();

        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to($attendanceLog->user)
            ->method(NotifyMethod::ONE)
            ->send();
    }

    /**
     * Handle the AttendanceLog "updated" event.
     */
    public function updated(AttendanceLog $attendanceLog): void
    {
        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to(RolesPermissionEnum::ADMIN['role'])
            ->method(NotifyMethod::BY_ROLE)
            ->send();

        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to($attendanceLog->user)
            ->method(NotifyMethod::ONE)
            ->send();
    }

    /**
     * Handle the AttendanceLog "deleted" event.
     */
    public function deleted(AttendanceLog $attendanceLog): void
    {
        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to(RolesPermissionEnum::ADMIN['role'])
            ->method(NotifyMethod::BY_ROLE)
            ->send();

        NotificationBuilder::make()
            ->notification(AttendanceEditedNotification::class)
            ->data([])
            ->to($attendanceLog->user)
            ->method(NotifyMethod::ONE)
            ->send();
    }

    /**
     * Handle the AttendanceLog "restored" event.
     */
    public function restored(AttendanceLog $attendanceLog): void
    {
        //
    }

    /**
     * Handle the AttendanceLog "force deleted" event.
     */
    public function forceDeleted(AttendanceLog $attendanceLog): void
    {
        //
    }
}
