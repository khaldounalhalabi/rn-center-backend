<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecretaryOnly
{
    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()?->isSecretary()) {
            return rest()
                ->notAuthorized()
                ->message(trans('site.unauthorized_user'))
                ->send();
        }
        return $next($request);
    }
}
