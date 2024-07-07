<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Balance\StoreUpdateBalanceRequest;
use App\Http\Resources\v1\BalanceResource;
use App\Models\Balance;
use App\Services\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends ApiController
{
    private BalanceService $balanceService;

    public function __construct()
    {
        $this->balanceService = BalanceService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->balanceService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(BalanceResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($balanceId)
    {
        /** @var Balance|null $item */
        $item = $this->balanceService->view($balanceId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BalanceResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateBalanceRequest $request)
    {
        /** @var Balance|null $item */
        $item = $this->balanceService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new BalanceResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($balanceId, StoreUpdateBalanceRequest $request)
    {
        /** @var Balance|null $item */
        $item = $this->balanceService->update($request->validated(), $balanceId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BalanceResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($balanceId)
    {
        $item = $this->balanceService->delete($balanceId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->balanceService->export($ids);
    }

    public function getImportExample()
    {
        return $this->balanceService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->balanceService->import();
    }
}
