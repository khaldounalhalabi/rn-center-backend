<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ClinicTransaction\StoreUpdateClinicTransactionRequest;
use App\Http\Resources\ClinicTransactionResource;
use App\Models\ClinicTransaction;
use App\Services\ClinicTransactionService;
use Illuminate\Http\Request;

class ClinicTransactionController extends ApiController
{
    private ClinicTransactionService $clinicTransactionService;

    public function __construct()
    {
        $this->clinicTransactionService = ClinicTransactionService::make();
        // place the relations you want to return them within the response
        $this->relations = ['appointment', 'appointment.customer.user'];
    }

    public function index()
    {
        $items = $this->clinicTransactionService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ClinicTransactionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($clinicTransactionId)
    {
        /** @var ClinicTransaction|null $item */
        $item = $this->clinicTransactionService->view($clinicTransactionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicTransactionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateClinicTransactionRequest $request)
    {
        /** @var ClinicTransaction|null $item */
        $item = $this->clinicTransactionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicTransactionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($clinicTransactionId, StoreUpdateClinicTransactionRequest $request)
    {
        /** @var ClinicTransaction|null $item */
        $item = $this->clinicTransactionService->update($request->validated(), $clinicTransactionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicTransactionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicTransactionId)
    {
        $item = $this->clinicTransactionService->delete($clinicTransactionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->clinicTransactionService->export($ids);
    }

    public function getImportExample()
    {
        return $this->clinicTransactionService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->clinicTransactionService->import();
    }
}
