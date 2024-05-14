<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotBlocked
{
    use RestTrait;

    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = auth()->user()->load('phones');
        if (!$authUser) {
            return $this->apiResponse(null, ApiController::STATUS_UNAUTHORIZED, __('site.unauthorized_user'));
        }

        if ($authUser->isBlocked()) {
            return $this->apiResponse(null, ApiController::STATUS_BLOCKED, __('site.your_account_has_been_blocked'));
        }
        return $next($request);
    }
}
