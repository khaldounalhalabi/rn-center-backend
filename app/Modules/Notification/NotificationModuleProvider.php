<?php

namespace App\Modules\Notification;

use App\Modules\Notification\App\Channels\DataBaseChannel;
use Illuminate\Notifications\Channels\DatabaseChannel as LaravelDatabaseChannel;
use Illuminate\Support\ServiceProvider;

class NotificationModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->instance(LaravelDatabaseChannel::class, new DataBaseChannel());
        $this->loadMigrationsFrom([
            app_path('Modules/Notification/database/migrations'),
        ]);
    }
}
