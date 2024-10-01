<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustAcceptContractMiddleware
{
    use RestTrait;

    /**
     * Handle an incoming request.
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $clinic = $user?->getClinic();

        if ($user?->isClinic() && !$clinic?->agreed_on_contract) {
            return $this->apiResponse(null, ApiController::STATUS_MUST_AGREE_ON_CONTRACT, trans('site.must_agree_contract'));
        }

        return $next($request);
    }
}
