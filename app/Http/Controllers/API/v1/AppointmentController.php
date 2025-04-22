<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Appointment\StoreUpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    private AppointmentService $appointmentService;

    public function __construct()
    {
        $this->appointmentService = AppointmentService::make();

        // place the relations you want to return them within the response
        $this->relations = ['customer.user', 'clinic.user', 'service'];
    }

    public function index()
    {
        $items = $this->appointmentService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AppointmentResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($appointmentId)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->view($appointmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdateAppointmentRequest $request)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($appointmentId, StoreUpdateAppointmentRequest $request)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->update($request->validated(), $appointmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentService->export($ids);
    }
}
