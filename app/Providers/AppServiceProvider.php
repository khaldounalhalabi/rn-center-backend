<?php

namespace App\Providers;

use App\Channels\DataBaseChannel;
use App\Models\ClinicTransaction;
use App\Models\Transaction;
use App\Observers\ClinicTransactionObserver;
use App\Observers\TransactionObserver;
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
        ClinicTransaction::observe(ClinicTransactionObserver::class);
        Transaction::observe(TransactionObserver::class);
        $this->app->instance(LaravelDatabaseChannel::class, new DataBaseChannel());
    }
}
