<?php

namespace App\Http\Middleware;

use App\Enums\RolesPermissionEnum;
use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMustVerifyEmail
{
    use RestTrait;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $this->apiResponse(null, ApiController::STATUS_NOT_AUTHENTICATED, __('site.unauthorized_user'));
        }

        if ($user->hasRole(RolesPermissionEnum::CUSTOMER['role']) && $user->email_verified_at == null) {
            return $this->apiResponse(null, ApiController::STATUS_UNAUTHORIZED, __('site.your_email_is_not_verified'));
        }

        return $next($request);
    }
}
