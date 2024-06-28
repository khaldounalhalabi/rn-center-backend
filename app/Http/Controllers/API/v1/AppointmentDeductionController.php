<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AppointmentDeduction\StoreUpdateAppointmentDeductionRequest;
use App\Http\Resources\AppointmentDeductionResource;
use App\Models\AppointmentDeduction;
use App\Services\AppointmentDeduction\AppointmentDeductionService;
use Illuminate\Http\Request;

class AppointmentDeductionController extends ApiController
{
    private AppointmentDeductionService $appointmentDeductionService;

    public function __construct()
    {
        $this->appointmentDeductionService = AppointmentDeductionService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->appointmentDeductionService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AppointmentDeductionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($appointmentDeductionId)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->view($appointmentDeductionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateAppointmentDeductionRequest $request)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($appointmentDeductionId, StoreUpdateAppointmentDeductionRequest $request)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->update($request->validated(), $appointmentDeductionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($appointmentDeductionId)
    {
        $item = $this->appointmentDeductionService->delete($appointmentDeductionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentDeductionService->export($ids);
    }

    public function getImportExample()
    {
        return $this->appointmentDeductionService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->appointmentDeductionService->import();
    }
}
