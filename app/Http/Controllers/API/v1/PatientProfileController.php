<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\PatientProfile\StoreUpdatePatientProfileRequest;
use App\Http\Resources\PatientProfileResource;
use App\Models\PatientProfile;
use App\Services\PatientProfile\IPatientProfileService;

class PatientProfileController extends ApiController
{
    private $patientProfileService;

    public function __construct(IPatientProfileService $patientProfileService)
    {

        $this->patientProfileService = $patientProfileService;

        // place the relations you want to return them within the response
        $this->relations = ['customer.user', 'clinic.user'];
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
}
