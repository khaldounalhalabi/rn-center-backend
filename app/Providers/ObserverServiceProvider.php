<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\AttendanceLog;
use App\Models\Medicine;
use App\Models\Payrun;
use App\Models\Transaction;
use App\Observers\AppointmentObserver;
use App\Observers\AttendanceLogObserver;
use App\Observers\MedicineObserver;
use App\Observers\PayrunObserver;
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
        Payrun::observe(PayrunObserver::class);
        AttendanceLog::observe(AttendanceLogObserver::class);
        Medicine::observe(MedicineObserver::class);
    }
}
