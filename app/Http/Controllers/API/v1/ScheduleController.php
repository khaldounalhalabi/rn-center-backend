<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Schedule\StoreUpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\User;
use App\Services\ScheduleService;

class ScheduleController extends ApiController
{
    private ScheduleService $scheduleService;

    public function __construct()
    {
        $this->scheduleService = ScheduleService::make();
        $this->relations = [];
    }

    public function clinicSchedules($clinicId)
    {
        $data = $this->scheduleService->getByScheduleable($clinicId);

        if (count($data)) {
            return $this->apiResponse(
                collect(ScheduleResource::collection($data))
                    ->groupBy('day_of_week'),
                self::STATUS_OK,
                __('site.get_successfully')
            );
        }

        return $this->noData([]);
    }

    public function storeUpdateSchedules(StoreUpdateScheduleRequest $request)
    {
        $item = $this->scheduleService->storeUpdateSchedules($request->validated());
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->noData([]);
    }

    public function deleteAllClinicSchedules($clinicId)
    {
        $result = $this->scheduleService->deleteAllSchedules($clinicId);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function deleteUserSchedules($userId)
    {
        $result = $this->scheduleService->deleteAllSchedules($userId, User::class);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function userSchedules($userId)
    {
        $data = $this->scheduleService->getByScheduleable($userId, User::class);

        if (count($data)) {
            return $this->apiResponse(
                collect(ScheduleResource::collection($data))
                    ->groupBy('day_of_week'),
                self::STATUS_OK,
                __('site.get_successfully')
            );
        }

        return $this->noData([]);
    }
}
