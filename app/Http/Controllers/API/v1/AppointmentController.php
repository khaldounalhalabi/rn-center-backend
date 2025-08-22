<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\PermissionEnum;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Appointment\ChangeAppointmentStatusRequest;
use App\Http\Requests\v1\Appointment\StoreUpdateAppointmentRequest;
use App\Http\Resources\v1\AppointmentResource;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use App\Services\AppointmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    private AppointmentService $appointmentService;

    public function __construct()
    {
        $this->appointmentService = AppointmentService::make();

        if (isAdmin() || can(PermissionEnum::APPOINTMENT_MANAGEMENT)) {
            $this->relations = ['customer.user', 'clinic.user', 'service', 'prescription', 'prescription.medicinePrescriptions.medicine'];
            $this->indexRelations = ['customer.user', 'clinic.user', 'service'];
        } elseif (isDoctor()) {
            $this->relations = ['customer.user', 'service', 'prescription', 'prescription.medicinePrescriptions.medicine'];
            $this->indexRelations = ['customer.user', 'service'];
        } else {
            $this->relations = ['clinic.user', 'service', 'prescription', 'prescription.medicinePrescriptions.medicine'];
            $this->indexRelations = ['clinic.user', 'service'];
        }
    }

    public function index()
    {
        $items = $this->appointmentService->indexWithPagination($this->indexRelations);
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

    public function getByCustomer($customerId = null)
    {
        if (isCustomer()) {
            $data = $this->appointmentService->paginateByCustomer(customer()->id, [
                'clinic.user',
                'service'
            ], $this->countable);
        } else {
            $data = $this->appointmentService->paginateByCustomer($customerId, [
                'clinic.user',
                'service'
            ], $this->countable);
        }

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

    public function cancelAppointment($appointmentId)
    {
        $result = $this->appointmentService->cancelAppointment($appointmentId);

        if (!$result) {
            return $this->noData();
        }

        return $this->apiResponse(
            AppointmentResource::make($result),
            self::STATUS_OK,
            trans('site.cancelled')
        );
    }

    public function todayAppointments()
    {
        $data = AppointmentRepository::make()
            ->globalQuery($this->indexRelations, $this->countable)
            ->whereDate('date_time', now()->format('Y-m-d'))
            ->when(isDoctor(), fn(Builder $query) => $query->where('clinic_id', clinic()->id))
            ->simplePaginate(request('per_page', 10));

        return $this->apiResponse(
            AppointmentResource::collection($data),
            self::STATUS_OK,
            trans('site.get_successfully'),
            paginationData($data)
        );
    }
}
