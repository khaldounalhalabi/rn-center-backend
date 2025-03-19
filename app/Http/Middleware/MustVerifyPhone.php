<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustVerifyPhone
{
    use RestTrait;

    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()?->isCustomer()) {
            return $this->apiResponse(null, ApiController::STATUS_UNAUTHORIZED, __('site.unauthorized_user'));
        }

        if (!auth()->user()?->hasVerifiedPhoneNumber()) {
            return $this->apiResponse(null, ApiController::STATUS_UNVERIFIED_PHONE_NUMBER, __('site.un_verified_phone'));
        }
        return $next($request);
    }
}
