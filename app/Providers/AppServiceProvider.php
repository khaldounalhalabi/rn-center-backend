<?php

namespace App\Providers;

use App\Channels\DataBaseChannel;
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

        Request::macro('isPost', function () {
            return $this->method() === 'POST';
        });

        Request::macro('isPut', function () {
            return $this->method() === 'PUT';
        });

        Carbon::macro('age', function ($date = null, $locale = "en") {
            $birthDate = Carbon::parse($date ?? $this);
            $now = Carbon::now();
            Carbon::setLocale($locale);
            if ($locale) {
                Carbon::setLocale($locale);
            }

            $years = $birthDate->diffInYears($now);
            if ($years > 0) {
                return $years . ' ' . Carbon::translateTimeString('years', "en" , $locale);
            }

            $months = $birthDate->diffInMonths($now);
            if ($months > 0) {
                return $months . ' ' . Carbon::translateTimeString('months', "en" , $locale);
            }

            $weeks = $birthDate->diffInWeeks($now);
            if ($weeks > 0) {
                return $weeks . ' ' . Carbon::translateTimeString('weeks', "en" , $locale);
            }

            $days = $birthDate->diffInDays($now);
            return $days . ' ' . Carbon::translateTimeString('days', "en" , $locale);
        });
    }
}
