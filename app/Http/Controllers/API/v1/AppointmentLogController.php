<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\AppointmentLog\StoreUpdateAppointmentLogRequest;
use App\Services\AppointmentLog\IAppointmentLogService;
use App\Http\Resources\AppointmentLogResource;
use App\Http\Controllers\ApiController;
use App\Models\AppointmentLog;
use Illuminate\Http\Request;

class AppointmentLogController extends ApiController
{
    private $appointmentLogService;

    public function __construct(IAppointmentLogService $appointmentLogService)
    {

        $this->appointmentLogService = $appointmentLogService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->appointmentLogService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AppointmentLogResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($appointmentLogId)
    {
        /** @var AppointmentLog|null $item */
        $item = $this->appointmentLogService->view($appointmentLogId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentLogResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateAppointmentLogRequest $request)
    {
        /** @var AppointmentLog|null $item */
        $item = $this->appointmentLogService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentLogResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($appointmentLogId, StoreUpdateAppointmentLogRequest $request)
    {
        /** @var AppointmentLog|null $item */
        $item = $this->appointmentLogService->update($request->validated(), $appointmentLogId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentLogResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($appointmentLogId)
    {
        $item = $this->appointmentLogService->delete($appointmentLogId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentLogService->export($ids);
    }

    public function getImportExample()
    {
        return $this->appointmentLogService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->appointmentLogService->import();
    }

    public function getAppointmentLogs($appointmentId)
    {
        $data = $this->appointmentLogService->getAppointmentLogs($appointmentId);

        return $this->apiResponse($data, self::STATUS_OK, __('site.get_successfully'));
    }
}
