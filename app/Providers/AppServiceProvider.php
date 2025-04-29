<?php

namespace App\Providers;

use App\Channels\DataBaseChannel;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Observers\AppointmentObserver;
use App\Observers\TransactionObserver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Channels\DatabaseChannel as LaravelDatabaseChannel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


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
        Schema::defaultStringLength(191);
        $this->app->instance(LaravelDatabaseChannel::class, new DataBaseChannel());
    }
}
