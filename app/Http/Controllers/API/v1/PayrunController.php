<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Payrun\StorePayrunRequest;
use App\Http\Requests\v1\PayRun\TogglePayrunStatusRequest;
use App\Http\Resources\v1\PayrunResource;
use App\Models\Payrun;
use App\Services\v1\Payrun\PayrunService;

class PayrunController extends ApiController
{
    private PayrunService $payrunService;

    public function __construct()
    {
        $this->payrunService = PayrunService::make();

        $this->relations = [];
        $this->countable = ['processedUsers', 'users', 'payslips', 'excludedUsers'];
    }

    public function index()
    {
        $items = $this->payrunService->indexWithPagination($this->relations, $this->countable);
        if ($items) {
            return $this->apiResponse(
                PayrunResource::collection($items['data'])->detailed(),
                self::STATUS_OK,
                __('site.get_successfully'),
                $items['pagination_data']
            );
        }

        return $this->noData([]);
    }

    public function show($payrunId)
    {
        /** @var Payrun|null $item */
        $item = $this->payrunService->view($payrunId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(PayrunResource::make($item)->detailed(), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StorePayrunRequest $request)
    {
        $payrun = $this->payrunService->create($request->validated());
        if ($payrun) {
            return $this->apiResponse(new PayRunResource($payrun), self::STATUS_OK, __('site.success'));
        } elseif ($payrun === false) {
            return $this->apiResponse(null, self::PAY_RUN_OVERLAP_ERROR, __('site.pay_run_overlap_error'));
        }
        return $this->noData();
    }

    public function destroy($payrunId)
    {
        $item = $this->payrunService->delete($payrunId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function reprocessPayrun($payrunId)
    {
        $result = $this->payrunService->reprocessPayrun($payrunId);

        if ($result) {
            return $this->apiResponse(
                PayRunResource::make($result)->detailed(),
                self::STATUS_OK,
                __('site.success')
            );
        }

        return $this->noData();
    }

    public function reportToExcel($payrunId)
    {
        $result = $this->payrunService->reportToExcel($payrunId);

        if (!$result) {
            return $this->noData();
        }

        return $result;
    }

    public function toggleStatus($payrunId, TogglePayrunStatusRequest $request)
    {
        $result = $this->payrunService->toggleStatus($payrunId, $request->validated('status'));
        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }
}
