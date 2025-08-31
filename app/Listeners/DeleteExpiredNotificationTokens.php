<?php

namespace App\Listeners;

use App\Models\FcmToken;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;

class DeleteExpiredNotificationTokens
{
    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        $report = Arr::get($event->data, 'report');

        $target = $report->target();

        FcmToken::where('token', $target->value())
            ->delete();
    }
}
