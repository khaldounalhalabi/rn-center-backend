<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ClinicHoliday\StoreUpdateClinicHolidayRequest;
use App\Http\Resources\ClinicHolidayResource;
use App\Models\ClinicHoliday;
use App\Services\ClinicHoliday\IClinicHolidayService;

class ClinicHolidayController extends ApiController
{
    private IClinicHolidayService $clinicHolidayService;

    public function __construct(IClinicHolidayService $clinicHolidayService)
    {

        $this->clinicHolidayService = $clinicHolidayService;

        // place the relations you want to return them within the response
        $this->relations = ['clinic'];
    }

    public function index()
    {
        $items = $this->clinicHolidayService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ClinicHolidayResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($clinicHolidayId)
    {
        /** @var ClinicHoliday|null $item */
        $item = $this->clinicHolidayService->view($clinicHolidayId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicHolidayResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateClinicHolidayRequest $request)
    {
        /** @var ClinicHoliday|null $item */
        $item = $this->clinicHolidayService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicHolidayResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($clinicHolidayId, StoreUpdateClinicHolidayRequest $request)
    {
        /** @var ClinicHoliday|null $item */
        $item = $this->clinicHolidayService->update($request->validated(), $clinicHolidayId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicHolidayResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicHolidayId)
    {
        $item = $this->clinicHolidayService->delete($clinicHolidayId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
