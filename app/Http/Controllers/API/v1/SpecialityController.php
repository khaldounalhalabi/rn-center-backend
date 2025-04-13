<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Speciality\StoreUpdateSpecialityRequest;
use App\Http\Resources\SpecialityResource;
use App\Models\Speciality;
use App\Services\SpecialityService;
use Illuminate\Http\Request;

class SpecialityController extends ApiController
{
    private SpecialityService $specialityService;

    public function __construct()
    {
        $this->specialityService = SpecialityService::make();
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

    public function getOrderedByClinicsCount()
    {
        $data = $this->specialityService->getOrderedByClinicsCount($this->relations, $this->countable);

        if ($data) {
            return $this->apiResponse(SpecialityResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->specialityService->export($ids);
    }

    public function getImportExample()
    {
        return $this->specialityService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->specialityService->import();
    }
}
