<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\PatientStudy\StoreUpdatePatientStudyRequest;
use App\Services\v1\PatientStudy\PatientStudyService;
use Throwable;

class PatientStudyController extends ApiController
{
    private PatientStudyService $patientStudyService;

    public function __construct()
    {
        $this->patientStudyService = PatientStudyService::make();
        $this->relations = [];
    }


    /**
     * @throws Throwable
     */
    public function store(StoreUpdatePatientStudyRequest $request)
    {
        $result = $this->patientStudyService->addStudyToCustomer($request->validated());
        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse($result, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function destroy($patientStudyId)
    {
        $item = $this->patientStudyService->delete($patientStudyId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
