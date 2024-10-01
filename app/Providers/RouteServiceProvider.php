<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     * Typically, users are redirected here after authentication.
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(['api', 'verified-phone', 'not_blocked', 'customer'])
                ->prefix('api/customer')
                ->name('api.customer.')
                ->group(base_path('routes/v1/api/customer.php'));

            Route::middleware(['api', 'not_blocked', 'doctor', 'contract'])
                ->prefix('api/doctor')
                ->group(base_path('routes/v1/api/doctor.php'));

            Route::middleware(['api', 'admin', 'not_blocked'])
                ->prefix('api/admin')
                ->name('api.admin.')
                ->group(base_path('routes/v1/api/admin.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'guest-header'])
                ->prefix('api')
                ->name('public.')
                ->group(base_path('routes/v1/api/public.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
