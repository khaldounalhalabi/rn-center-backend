<?php

namespace App\Providers;

use App\Channels\DataBaseChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\Channels\DatabaseChannel as LaravelDatabaseChannel;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->instance(LaravelDatabaseChannel::class, new DataBaseChannel());
    }
}
