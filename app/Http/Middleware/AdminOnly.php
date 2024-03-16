<?php

namespace App\Http\Middleware;

use App\Enums\RolesPermissionEnum;
use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    use RestTrait;
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()?->hasRole(RolesPermissionEnum::ADMIN['role'])){
            return $this->apiResponse(null  , ApiController::STATUS_UNAUTHORIZED , __('site.unauthorized_user'));
        }
        return $next($request);
    }
}
