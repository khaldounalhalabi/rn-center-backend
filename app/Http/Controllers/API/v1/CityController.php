<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\City\StoreUpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Services\City\ICityService;

class CityController extends ApiController
{
    private $cityService;

    public function __construct(ICityService $cityService)
    {

        $this->cityService = $cityService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->cityService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(CityResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($cityId)
    {
        /** @var City|null $item */
        $item = $this->cityService->view($cityId, $this->relations);
        if ($item) {
            return $this->apiResponse(new CityResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateCityRequest $request)
    {
        /** @var City|null $item */
        $item = $this->cityService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new CityResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($cityId, StoreUpdateCityRequest $request)
    {
        /** @var City|null $item */
        $item = $this->cityService->update($request->validated(), $cityId, $this->relations);
        if ($item) {
            return $this->apiResponse(new CityResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($cityId)
    {
        $item = $this->cityService->delete($cityId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
