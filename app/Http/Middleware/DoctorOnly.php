<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorOnly
{
    use RestTrait;

    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $isDoctor = $user?->isDoctor();
        $isEmployee = $user?->isClinicEmployee();

        if ((!$user) || (!$isDoctor && !$isEmployee)) {
            return $this->apiResponse(null, ApiController::STATUS_UNAUTHORIZED, __('site.unauthorized_user'));
        }

        if ($isDoctor && !$user->clinic?->hasActiveSubscription()){
            return $this->apiResponse(null, ApiController::STATUS_EXPIRED_SUBSCRIPTION, __('site.expired_subscription'));
        }

        if ($isEmployee && !$user->clinicEmployee?->clinic?->hasActiveSubscription()){
            return $this->apiResponse(null, ApiController::STATUS_EXPIRED_SUBSCRIPTION, __('site.expired_subscription'));
        }

        return $next($request);
    }
}
