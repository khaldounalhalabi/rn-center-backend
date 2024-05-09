<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AvailableDepartment\StoreUpdateAvailableDepartmentRequest;
use App\Http\Resources\AvailableDepartmentResource;
use App\Models\AvailableDepartment;
use App\Services\AvailableDepartment\IAvailableDepartmentService;

class AvailableDepartmentController extends ApiController
{
    private $availableDepartmentService;

    public function __construct(IAvailableDepartmentService $availableDepartmentService)
    {

        $this->availableDepartmentService = $availableDepartmentService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->availableDepartmentService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AvailableDepartmentResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($availableDepartmentId)
    {
        /** @var AvailableDepartment|null $item */
        $item = $this->availableDepartmentService->view($availableDepartmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AvailableDepartmentResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateAvailableDepartmentRequest $request)
    {
        /** @var AvailableDepartment|null $item */
        $item = $this->availableDepartmentService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AvailableDepartmentResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($availableDepartmentId, StoreUpdateAvailableDepartmentRequest $request)
    {
        /** @var AvailableDepartment|null $item */
        $item = $this->availableDepartmentService->update($request->validated(), $availableDepartmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AvailableDepartmentResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($availableDepartmentId)
    {
        $item = $this->availableDepartmentService->delete($availableDepartmentId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
