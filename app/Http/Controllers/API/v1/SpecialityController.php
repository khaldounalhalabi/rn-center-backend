<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Speciality\StoreUpdateSpecialityRequest;
use App\Http\Resources\SpecialityResource;
use App\Models\Speciality;
use App\Services\Speciality\ISpecialityService;

class SpecialityController extends ApiController
{
    private $specialityService;

    public function __construct(ISpecialityService $specialityService)
    {

        $this->specialityService = $specialityService;

        // place the relations you want to return them within the response
        $this->relations = ['media'];
    }

    public function index()
    {
        $items = $this->specialityService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(SpecialityResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($specialityId)
    {
        /** @var Speciality|null $item */
        $item = $this->specialityService->view($specialityId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SpecialityResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateSpecialityRequest $request)
    {
        /** @var Speciality|null $item */
        $item = $this->specialityService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new SpecialityResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($specialityId, StoreUpdateSpecialityRequest $request)
    {
        /** @var Speciality|null $item */
        $item = $this->specialityService->update($request->validated(), $specialityId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SpecialityResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($specialityId)
    {
        $item = $this->specialityService->delete($specialityId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
