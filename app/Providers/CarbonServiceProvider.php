<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class CarbonServiceProvider extends ServiceProvider
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
        Carbon::macro('age', function ($date = null, $locale = "en") {
            $birthDate = Carbon::parse($date ?? $this);
            $now = Carbon::now();
            Carbon::setLocale($locale);
            if ($locale) {
                Carbon::setLocale($locale);
            }

            $years = $birthDate->diffInYears($now);
            if ($years > 0) {
                return $years . ' ' . Carbon::translateTimeString('years', "en", $locale);
            }

            $months = $birthDate->diffInMonths($now);
            if ($months > 0) {
                return $months . ' ' . Carbon::translateTimeString('months', "en", $locale);
            }

            $weeks = $birthDate->diffInWeeks($now);
            if ($weeks > 0) {
                return $weeks . ' ' . Carbon::translateTimeString('weeks', "en", $locale);
            }

            $days = $birthDate->diffInDays($now);
            return $days . ' ' . Carbon::translateTimeString('days', "en", $locale);
        });
    }
}
