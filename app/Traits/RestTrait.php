<?php

namespace App\Traits;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

trait RestTrait
{
    /**
     * @param mixed $response
     * @return JsonResponse
     */
    public function noData(mixed $response = null): JsonResponse
    {
        return $this->apiResponse($response, ApiController::STATUS_OK, __('site.there_is_no_data'));
    }

    /**
     * this function will determine the api response structure to make all responses has the same structure
     * @param null $data
     * @param null $message
     * @param null $paginate
     */
    public function apiResponse($data = null, int $code = 200, $message = null, $paginate = null): JsonResponse
    {
        $arrayResponse = [
            'data' => $data,
            'status' => $code == 200 || $code == 201 || $code == 204 || $code == 205,
            'message' => $message,
            'code' => $code,
            'paginate' => $paginate,
        ];

        return response()->json($arrayResponse, $code, [], JSON_PRETTY_PRINT);
    }
}
