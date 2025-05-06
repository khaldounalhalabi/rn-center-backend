<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Services\v1\PayslipAdjustment\PayslipAdjustmentService;

class PayslipAdjustmentController extends ApiController
{
    private PayslipAdjustmentService $payslipAdjustmentService;

    public function __construct()
    {
        $this->payslipAdjustmentService = PayslipAdjustmentService::make();
        $this->relations = [];
    }

    public function destroy($payslipAdjustmentId)
    {
        $item = $this->payslipAdjustmentService->delete($payslipAdjustmentId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
