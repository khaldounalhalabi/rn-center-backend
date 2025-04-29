<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
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
        Request::macro('isPost', function () {
            return $this->method() === 'POST';
        });

        Request::macro('isPut', function () {
            return $this->method() === 'PUT';
        });
    }
}
