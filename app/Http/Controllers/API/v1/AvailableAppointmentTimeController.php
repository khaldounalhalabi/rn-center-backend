<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\AvailableAppointmentTime\GetAvailableAppointmentTimesRequest;
use App\Services\AvailableAppointmentTimeService;

class AvailableAppointmentTimeController extends ApiController
{
    private AvailableAppointmentTimeService $service;

    public function __construct()
    {
        $this->service = AvailableAppointmentTimeService::make();
    }

    public function get(GetAvailableAppointmentTimesRequest $request)
    {
        $slots = $this->service->getAvailableTimeSlots($request->validated('clinic_id'), $request->validated('date'));
        return $this->apiResponse(
            $slots->map->format('H:i')->values()->toArray(),
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }
}
