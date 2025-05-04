<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Appointment\ChangeAppointmentStatusRequest;
use App\Http\Requests\v1\Appointment\StoreUpdateAppointmentRequest;
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
        $this->relations = ['customer.user', 'clinic.user', 'service', 'prescription', 'prescription.medicinePrescriptions.medicine'];
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

    public function changeAppointmentStatus(ChangeAppointmentStatusRequest $request)
    {
        $appointment = $this->appointmentService->changeAppointmentStatus($request->validated(), $this->relations, $this->countable);
        if ($appointment) {
            return $this->apiResponse(
                AppointmentResource::make($appointment),
                self::STATUS_OK,
                trans('site.update_successfully')
            );
        }

        return $this->noData();
    }

    public function getByClinic($clinicId)
    {
        $data = $this->appointmentService->paginateByClinic($clinicId, [
            'customer.user',
            'service'

        ], $this->countable);
        if ($data) {
            return $this->apiResponse(
                AppointmentResource::collection($data['data']),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }

    public function getByCustomer($customerId)
    {
        $data = $this->appointmentService->paginateByCustomer($customerId, [
            'clinic.user',
            'service'
        ], $this->countable);

        if ($data) {
            return $this->apiResponse(
                AppointmentResource::collection($data['data']),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
