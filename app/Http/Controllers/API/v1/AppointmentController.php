<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\Appointment\StoreUpdateAppointmentRequest;
use App\Services\Appointment\IAppointmentService;
use App\Http\Resources\AppointmentResource;
use App\Http\Controllers\ApiController;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    private IAppointmentService $appointmentService;

    public function __construct(IAppointmentService $appointmentService)
    {

        $this->appointmentService = $appointmentService;

        // place the relations you want to return them within the response
        $this->relations = ['clinic', 'clinic.user', 'customer.user', 'service'];
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

        return $this->noData(null);
    }

    public function store(StoreUpdateAppointmentRequest $request)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($appointmentId, StoreUpdateAppointmentRequest $request)
    {
        /** @var Appointment|null $item */
        $item = $this->appointmentService->update($request->validated(), $appointmentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($appointmentId)
    {
        $item = $this->appointmentService->delete($appointmentId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentService->export($ids);
    }

    public function getImportExample()
    {
        return $this->appointmentService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);
        $this->appointmentService->import();
    }
}
