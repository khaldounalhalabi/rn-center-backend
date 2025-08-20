<?php

namespace App\Exceptions;

use App\Exceptions\Application\ApplicationException;
use App\Http\Controllers\ApiController;
use App\Traits\RestTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    use RestTrait;

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * A list of the exception types that are not reported.
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * @throws Throwable
     */
    public function handleException($request, Throwable $exception): \Illuminate\Http\Response|JsonResponse|RedirectResponse|Response
    {
        if ($exception instanceof AuthenticationException) {
            return $this->apiResponse('', ApiController::STATUS_NOT_AUTHENTICATED, $exception->getMessage());
        }

        if ($exception instanceof AuthorizationException) {
            return $this->apiResponse('', ApiController::STATUS_UNAUTHORIZED, $exception->getMessage());
        }

        if ($exception instanceof HttpException) {
            if ($exception->getMessage() == 'Unauthorized Action') {
                return $this->apiResponse('', ApiController::STATUS_FORBIDDEN, $exception->getMessage());
            }

            return $this->apiResponse('', ApiController::STATUS_BAD_REQUEST, $exception->getMessage());
        }

        if ($exception instanceof HttpResponseException) {
            return $this->apiResponse('', ApiController::STATUS_FORBIDDEN, $exception->getMessage());
        }

        if ($exception instanceof ValidationException) {
            $msg = [
                'errors' => collect($exception->errors())->map(fn($error) => $error[0]),
            ];

            return $this->apiResponse('', ApiController::STATUS_VALIDATION, $msg);
        }
        if ($exception instanceof ModelNotFoundException) {
            return $this->apiResponse('', ApiController::STATUS_NOT_FOUND, $exception->getMessage());
        }
        if ($exception instanceof RouteNotFoundException) {
            if ($exception->getMessage() == 'Route [login] not defined.') {
                return $this->apiResponse('', ApiController::STATUS_NOT_AUTHENTICATED, 'you should login');
            }
        }

        if ($exception instanceof UnauthorizedException) {
            return $this->apiResponse('', ApiController::STATUS_UNAUTHORIZED, $exception->getMessage());
        }

        if ($exception instanceof ApprovingPayslipsWithRejectedPayslips) {
            return $this->apiResponse(
                null,
                $exception->getCode(),
                $exception->getMessage(),
            );
        }

        if ($exception instanceof ApplicationException) {
            return $this->apiResponse(
                null,
                $exception->getCode(),
                $exception->getMessage(),
            );
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->apiResponse('', 500, $exception->getMessage());
    }

    public function render($request, Throwable $exception): \Illuminate\Http\Response|JsonResponse|RedirectResponse|Response
    {
        if (!$request->acceptsHtml()) {
            return $this->handleException($request, $exception);
        }
        return parent::render($request, $exception);

    }

    /**
     * Register the exception handling callbacks for the application.
     * @return void
     */
    public function register(): void
    {
        //
    }
}
