<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Transaction;
use App\Observers\AppointmentObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
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
        Transaction::observe(TransactionObserver::class);
        Appointment::observe(AppointmentObserver::class);
    }
}
