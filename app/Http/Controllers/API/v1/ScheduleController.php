<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Schedule\StoreUpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Services\Schedule\IScheduleService;

class ScheduleController extends ApiController
{
    private IScheduleService $scheduleService;

    public function __construct(IScheduleService $scheduleService)
    {

        $this->scheduleService = $scheduleService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function clinicSchedules($clinicId)
    {
        $data = $this->scheduleService->getClinicSchedule($clinicId);

        if (count($data)) {
            return $this->apiResponse(collect(ScheduleResource::collection($data))->groupBy('day_of_week'), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData([]);
    }

    public function index()
    {
        $items = $this->scheduleService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ScheduleResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($scheduleId)
    {
        /** @var Schedule|null $item */
        $item = $this->scheduleService->view($scheduleId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ScheduleResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function storeUpdateSchedules(StoreUpdateScheduleRequest $request)
    {
        $item = $this->scheduleService->storeUpdateSchedules($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->noData([]);
    }

    public function destroy($scheduleId)
    {
        $item = $this->scheduleService->delete($scheduleId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function deleteAllClinicSchedules($clinicId)
    {
        $result = $this->scheduleService->deleteAllClinicSchedules($clinicId);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
