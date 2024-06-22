<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClinicEmployeeHasPermission
{
    use RestTrait;

    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $permission, string $model): Response
    {
        if (auth()->user()?->isClinicEmployee() && !auth()->user()?->hasPermission($permission, $model)) {
            return $this->apiResponse(null, ApiController::STATUS_UNAUTHORIZED, __('site.unauthorized_user'));
        }
        return $next($request);
    }
}
