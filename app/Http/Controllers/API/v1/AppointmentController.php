<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Appointment\StoreUpdateAppointmentRequest;
use App\Http\Requests\Appointment\ToggleAppointmentStatusRequest;
use App\Http\Requests\Appointment\UpdateAppointmentDateRequest;
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

        if (auth()->user()?->isClinic()) {
            $this->relations = ['customer.user', 'service'];
        } elseif (auth()->user()?->isCustomer()) {
            $this->relations = ['service', 'clinic',];
        } else {
            $this->relations = ['clinic', 'clinic.user', 'customer.user', 'service'];
        }
    }

    public function show($appointmentId)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->view($appointmentId, [...$this->relations, 'cancelLog']);
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

        return $this->apiResponse(null, self::STATUS_INVALID_TIME_TO_BOOK, __('site.doctor_dont_has_vacant_in_this_time'));
    }

    public function update($appointmentId, StoreUpdateAppointmentRequest $request)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->update($request->validated(), $appointmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_INVALID_TIME_TO_BOOK, __('site.doctor_dont_has_vacant_in_this_time'));
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentService->export($ids);
    }

    public function getClinicAppointments($clinicId)
    {
        $data = $this->appointmentService->getClinicAppointments($clinicId, $this->relations);
        if ($data) {
            return $this->apiResponse(AppointmentResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function toggleAppointmentStatus($appointmentId, ToggleAppointmentStatusRequest $request)
    {
        $result = $this->appointmentService->toggleAppointmentStatus($appointmentId, $request->validated());

        if ($result) {
            return $this->apiResponse(new AppointmentResource($result), self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function getCustomerLastAppointment($customerId, $clinicId = null)
    {
        if (!$clinicId) {
            $clinicId = auth()->user()?->getClinicId();
        }
        $appointment = $this->appointmentService->getCustomerLastAppointment($customerId, $clinicId, $this->relations, $this->countable);

        if ($appointment) {
            return $this->apiResponse(new AppointmentResource($appointment), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function updateAppointmentDate($appointmentId, UpdateAppointmentDateRequest $request)
    {
        $appointment = $this
            ->appointmentService
            ->updateAppointmentDate($appointmentId,
                $request->validated()['date'],
                $this->relations,
                $this->countable);
        if ($appointment) {
            return $this->apiResponse(new AppointmentResource($appointment), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_INVALID_TIME_TO_BOOK, __('site.doctor_dont_has_vacant_in_this_time'));
    }

    public function all()
    {
        $data = $this->appointmentService->index($this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(AppointmentResource::collection($data), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function index()
    {
        $items = $this->appointmentService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AppointmentResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function getCustomerTodayAppointments()
    {
        $data = $this->appointmentService->getCustomerTodayAppointments($this->relations, $this->countable);
        return $this->apiResponse(AppointmentResource::collection($data), self::STATUS_OK, __('site.get_successfully'));
    }

    public function getByCustomer($customerId)
    {
        $data = $this->appointmentService->getByCustomer($customerId, $this->relations, $this->countable);

        if ($data) {
            return $this->apiResponse(AppointmentResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }

    public function todayAppointments()
    {
        $data = $this->appointmentService->getClinicTodayAppointments($this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(AppointmentResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }

    public function getAppointmentsCountInMonth()
    {
        return $this->apiResponse(
            $this->appointmentService->appointmentsCountInMonth(),
            self::STATUS_OK,
            __('site.get_successfully'),
        );
    }

    public function getAppointmentsCompletedCountInMonth()
    {
        return $this->apiResponse(
            $this->appointmentService->getAllCompletedCountMonthly(),
            self::STATUS_OK,
            __('site.get_successfully'),
        );
    }

    public function recentAppointments()
    {
        $data = $this->appointmentService->recentAppointments(['customer.user']);
        if ($data) {
            return $this->apiResponse(AppointmentResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function customerCancelAppointment($appointmentId)
    {
        $appointment = $this->appointmentService->customerCancelAppointment($appointmentId);
        if ($appointment) {
            return $this->apiResponse(new AppointmentResource($appointment), self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function getByCode($code)
    {
        $appointment = $this->appointmentService->getByCode($code, [
            'lastBookedLog',
            'lastCheckinLog',
            'lastCheckoutLog',
            'lastCancelledLog',
            ...$this->relations,
        ], [...$this->countable, 'beforeAppointments']);
        if ($appointment) {
            return $this->apiResponse(new AppointmentResource($appointment), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }
}
