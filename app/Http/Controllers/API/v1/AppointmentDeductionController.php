<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AppointmentDeduction\StoreUpdateAppointmentDeductionRequest;
use App\Http\Resources\AppointmentDeductionResource;
use App\Models\AppointmentDeduction;
use App\Services\AppointmentDeductionService;
use Illuminate\Http\Request;

class AppointmentDeductionController extends ApiController
{
    private AppointmentDeductionService $appointmentDeductionService;

    public function __construct()
    {
        $this->appointmentDeductionService = AppointmentDeductionService::make();

        // place the relations you want to return them within the response
        if (auth()->user()?->isClinic()) {
            $this->relations = ['appointment'];
        } else {
            $this->relations = ['clinic.user', 'appointment'];
        }
    }

    public function index()
    {
        $items = $this->appointmentDeductionService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AppointmentDeductionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($appointmentDeductionId)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->view($appointmentDeductionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateAppointmentDeductionRequest $request)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($appointmentDeductionId, StoreUpdateAppointmentDeductionRequest $request)
    {
        /** @var AppointmentDeduction|null $item */
        $item = $this->appointmentDeductionService->update($request->validated(), $appointmentDeductionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AppointmentDeductionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($appointmentDeductionId)
    {
        $item = $this->appointmentDeductionService->delete($appointmentDeductionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->appointmentDeductionService->export($ids);
    }

    public function toggleStatus($appointmentDeductionId)
    {
        $result = $this->appointmentDeductionService->toggleStatus($appointmentDeductionId);

        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function getByClinic($clinicId)
    {
        $data = $this->appointmentDeductionService->getByClinic($clinicId, ['appointment'], $this->countable);

        if ($data) {
            return $this->apiResponse(AppointmentDeductionResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function clinicSummary()
    {
        return $this->apiResponse(
            $this->appointmentDeductionService->clinicSummary() ,
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }

    public function all()
    {
        $data = $this->appointmentDeductionService->index($this->relations , $this->countable);
        if ($data){
            return $this->apiResponse(AppointmentDeductionResource::collection($data) , self::STATUS_OK , __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function adminSummary()
    {
        return $this->apiResponse(
            $this->appointmentDeductionService->adminSummary(),
            self::STATUS_OK ,
            __('site.get_successfully')
        );
    }
}
