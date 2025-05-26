<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustVerifyPhone
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (user()->isAdmin()){
            return $next($request);
        }

        if (is_null(user()->phone_verified_at)){
            return rest()
                ->unverifiedPhone()
                ->send();
        }

        return $next($request);
    }
}
