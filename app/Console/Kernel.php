<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('app:send-appointment-five-minutes-remaining-notifications')
            ->withoutOverlapping()
            ->everyFiveMinutes();

        $schedule->command('app:send-appointment-thirty-minutes-remaining-notifications')
            ->withoutOverlapping()
            ->everyThirtyMinutes();

        $schedule->command('app:send-appointment-one-hour-remaining-notifications')
            ->withoutOverlapping()
            ->hourly();

        $schedule->command('app:send-appointment-one-day-remaining-notifications')
            ->withoutOverlapping()
            ->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
