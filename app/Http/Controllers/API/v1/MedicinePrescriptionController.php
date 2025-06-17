<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Services\v1\MedicinePrescription\MedicinePrescriptionService;

class MedicinePrescriptionController extends ApiController
{
    private MedicinePrescriptionService $service;

    public function __construct()
    {
        $this->service = MedicinePrescriptionService::make();
    }

    public function toggleStatus($medicinePrescriptionId)
    {
        $result = $this->service->toggleStatus($medicinePrescriptionId);
        if ($result) {
            return $this->apiResponse(
                $result,
                self::STATUS_OK,
                trans('site.success')
            );
        }

        return $this->noData();
    }
}
