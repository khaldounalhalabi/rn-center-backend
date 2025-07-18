<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Payslip\BulkAdjustmentRequest;
use App\Http\Requests\v1\Payslip\BulkPdfDownloadRequest;
use App\Http\Requests\v1\Payslip\TogglePayslipStatusRequest;
use App\Http\Requests\v1\Payslip\UpdatePayslipRequest;
use App\Http\Requests\v1\PayslipAdjustment\StoreUpdatePayslipAdjustmentRequest;
use App\Http\Resources\v1\PayslipResource;
use App\Modules\PDF;
use App\Services\v1\Payslip\PayslipService;
use Throwable;

class PayslipController extends ApiController
{
    private PayslipService $payslipService;

    public function __construct()
    {
        $this->payslipService = PayslipService::make();

        $this->relations = ['payslipAdjustments', 'user.roles', 'formula', 'user.clinic', 'payrun'];
    }

    public function show($payslipId)
    {
        $item = $this->payslipService->view($payslipId, $this->relations);
        if ($item) {
            return $this->apiResponse(PayslipResource::make($item)->detailed(), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function getByPayrun($payrunId)
    {
        $data = $this->payslipService->getByPayrun($payrunId, $this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(
                PayslipResource::collection($data['data'])->detailed(),
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['pagination_data']
            );
        }
        return $this->noData();
    }

    /**
     * @throws Throwable
     */
    public function addAdjustment($payslipId, StoreUpdatePayslipAdjustmentRequest $request)
    {
        $data = $this->payslipService->addAdjustment($payslipId, $request->validated(), $this->relations);
        if ($data) {
            return $this->apiResponse(
                PayslipResource::make($data)->detailed(),
                self::STATUS_OK,
                __('site.success')
            );
        }

        return $this->noData();
    }

    public function toPdf($payslipId)
    {
        $result = $this->payslipService->toPdf($payslipId);

        if (!$result) {
            return $this->noData();
        }

        return PDF::pdfResponse($result);
    }

    public function bulkPdfDownload(BulkPdfDownloadRequest $request)
    {
        $data = $request->validated();
        $result = $this->payslipService->bulkPdfDownload($data);

        if (!$result) {
            return $this->noData();
        }

        return PDF::pdfResponse($result);
    }

    public function toggleStatus($payslipId, TogglePayslipStatusRequest $request)
    {
        $result = $this->payslipService->toggleStatus($payslipId, $request->validated('status'));
        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function update(UpdatePayslipRequest $request, $payslipId)
    {
        $data = $this->payslipService->update($request->validated(), $payslipId, $this->relations, $this->countable);

        if ($data) {
            return $this->apiResponse(
                PayslipResource::make($data)->detailed(),
                self::STATUS_OK,
                __('site.update_successfully')
            );
        }

        return $this->noData();
    }

    public function bulkAdjustment(BulkAdjustmentRequest $request)
    {
        $updatedCount = $this->payslipService->bulkAdjustment($request->validated());
        return $this->apiResponse([
            'affected_payslips' => $updatedCount,
        ], self::STATUS_OK, __('site.update_successfully'));
    }

    public function mine()
    {
        $data = $this->payslipService->mine([
            'payslipAdjustments',
            'formula',
            'payrun'
        ], $this->countable);

        if ($data) {
            return $this->apiResponse(
                PayslipResource::collection($data['data'])->detailed(),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
