<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\MedicalRecord\StoreUpdateMedicalRecordRequest;
use App\Http\Resources\v1\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Services\v1\MedicalRecord\MedicalRecordService;

class MedicalRecordController extends ApiController
{
    private MedicalRecordService $medicalRecordService;

    public function __construct()
    {
        $this->medicalRecordService = MedicalRecordService::make();
        if (isCustomer()) {
            $this->relations = ['clinic.user'];
        } else {
            $this->relations = [];
        }
    }

    public function index()
    {
        $items = $this->medicalRecordService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(MedicalRecordResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($medicalRecordId)
    {
        /** @var MedicalRecord|null $item */
        $item = $this->medicalRecordService->view($medicalRecordId, $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicalRecordResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateMedicalRecordRequest $request)
    {
        /** @var MedicalRecord|null $item */
        $item = $this->medicalRecordService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicalRecordResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($medicalRecordId, StoreUpdateMedicalRecordRequest $request)
    {
        /** @var MedicalRecord|null $item */
        $item = $this->medicalRecordService->update($request->validated(), $medicalRecordId, $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicalRecordResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($medicalRecordId)
    {
        $item = $this->medicalRecordService->delete($medicalRecordId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getByCustomer($customerId)
    {
        $items = $this->medicalRecordService->getByCustomer($customerId, $this->relations);
        if ($items) {
            return $this->apiResponse(MedicalRecordResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }
}
