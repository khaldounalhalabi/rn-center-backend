<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Hospital\StoreUpdateHospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Models\Hospital;
use App\Services\HospitalService;

class HospitalController extends ApiController
{
    private HospitalService $hospitalService;

    public function __construct()
    {
        $this->hospitalService = HospitalService::make();

        // place the relations you want to return them within the response
        $this->relations = ['phones', 'media', 'address', 'address.city'];
    }

    public function getAll()
    {
        return $this->hospitalService->index($this->relations);
    }

    public function index()
    {
        $items = $this->hospitalService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(HospitalResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($hospitalId)
    {
        /** @var Hospital|null $item */
        $item = $this->hospitalService->view($hospitalId, $this->relations);
        if ($item) {
            return $this->apiResponse(new HospitalResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdateHospitalRequest $request)
    {
        /** @var Hospital|null $item */
        $item = $this->hospitalService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new HospitalResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($hospitalId, StoreUpdateHospitalRequest $request)
    {
        /** @var Hospital|null $item */
        $item = $this->hospitalService->update($request->validated(), $hospitalId, $this->relations);
        if ($item) {
            return $this->apiResponse(new HospitalResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function destroy($hospitalId)
    {
        $item = $this->hospitalService->delete($hospitalId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function toggleHospitalStatus($hospitalId)
    {
        $result = $this->hospitalService->toggleHospitalStatus($hospitalId);

        if ($result) {
            return $this->apiResponse(new HospitalResource($result), self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function getByUserCity()
    {
        $data = $this->hospitalService->getByUserCity(auth()->user());

        if ($data) {
            return $this->apiResponse(HospitalResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
