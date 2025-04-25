<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Holiday\StoreUpdateHolidayRequest;
use App\Http\Resources\v1\HolidayResource;
use App\Models\Holiday;
use App\Services\v1\Holiday\HolidayService;
use Illuminate\Http\Request;

class HolidayController extends ApiController
{
    private HolidayService $holidayService;

    public function __construct()
    {
        $this->holidayService = HolidayService::make();
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->holidayService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(HolidayResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($holidayId)
    {
        /** @var Holiday|null $item */
        $item = $this->holidayService->view($holidayId, $this->relations);
        if ($item) {
            return $this->apiResponse(new HolidayResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateHolidayRequest $request)
    {
        /** @var Holiday|null $item */
        $item = $this->holidayService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new HolidayResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($holidayId, StoreUpdateHolidayRequest $request)
    {
        /** @var Holiday|null $item */
        $item = $this->holidayService->update($request->validated(), $holidayId, $this->relations);
        if ($item) {
            return $this->apiResponse(new HolidayResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($holidayId)
    {
        $item = $this->holidayService->delete($holidayId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->holidayService->export($ids);
    }

    public function getImportExample()
    {
        return $this->holidayService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->holidayService->import();
    }

    public function activeHolidays()
    {
        return $this->apiResponse(
            HolidayResource::collection($this->holidayService->activeHolidays()),
            self::STATUS_OK,
            trans('site.get_successfully'),
        );
    }
}
