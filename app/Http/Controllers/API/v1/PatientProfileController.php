<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\PatientProfile\StoreUpdatePatientProfileRequest;
use App\Http\Resources\PatientProfileResource;
use App\Models\PatientProfile;
use App\Services\PatientProfileService;

class PatientProfileController extends ApiController
{
    private PatientProfileService $patientProfileService;

    public function __construct()
    {
        $this->patientProfileService = PatientProfileService::make();

        // place the relations you want to return them within the response
        if (auth()->user()?->isCustomer()) {
            $this->relations = ['clinic.user', 'media'];
        } else {
            $this->relations = ['customer.user', 'clinic.user', 'media'];
        }
    }

    public function index()
    {
        $items = $this->patientProfileService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(PatientProfileResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($patientProfileId)
    {
        /** @var PatientProfile|null $item */
        $item = $this->patientProfileService->view($patientProfileId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PatientProfileResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdatePatientProfileRequest $request)
    {
        /** @var PatientProfile|null $item */
        $item = $this->patientProfileService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new PatientProfileResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($patientProfileId, StoreUpdatePatientProfileRequest $request)
    {
        /** @var PatientProfile|null $item */
        $item = $this->patientProfileService->update($request->validated(), $patientProfileId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PatientProfileResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($patientProfileId)
    {
        $item = $this->patientProfileService->delete($patientProfileId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getByCurrentCustomer()
    {
        $data = $this->patientProfileService->getCustomerPatientProfiles(auth()?->user()?->customer?->id, $this->relations);
        if ($data) {
            return $this->apiResponse($data['data'], self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }

    public function getCustomerPatientProfiles($customerId)
    {
        $data = $this->patientProfileService->getCustomerPatientProfiles($customerId, $this->relations);
        if ($data) {
            return $this->apiResponse($data['data'], self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }
}
