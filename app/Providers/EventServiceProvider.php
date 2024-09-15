<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\ClinicTransaction;
use App\Models\Transaction;
use App\Observers\AppointmentObserver;
use App\Observers\ClinicTransactionObserver;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        ClinicTransaction::observe(ClinicTransactionObserver::class);
        Transaction::observe(TransactionObserver::class);
        Appointment::observe(AppointmentObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
