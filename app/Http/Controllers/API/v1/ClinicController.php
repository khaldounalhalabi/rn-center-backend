<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Clinic\StoreUpdateClinicRequest;
use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use App\Services\ClinicService;
use Throwable;

class ClinicController extends ApiController
{
    private ClinicService $clinicService;

    public function __construct()
    {
        $this->clinicService = ClinicService::make();
        if (!auth()->user() || isCustomer()) {
            $this->indexRelations = ['specialities', 'user',];
            $this->countable = [];
            $this->relations = ['user', 'specialities', 'schedules'];
        } else {
            $this->indexRelations = ['user'];
            $this->countable = ['appointments', 'todayAppointments', 'upcomingAppointments'];
            $this->relations = ['user', 'specialities'];
        }
    }

    public function index()
    {
        $items = $this->clinicService->indexWithPagination($this->indexRelations, $this->countable);
        if ($items) {
            return $this->apiResponse(ClinicResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($clinicId)
    {
        /** @var Clinic|null $item */
        $item = $this->clinicService->view($clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    /**
     * @throws Throwable
     */
    public function store(StoreUpdateClinicRequest $request)
    {
        /** @var Clinic|null $item */
        $item = $this->clinicService->store($request->validated(), $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function destroy($clinicId)
    {
        $item = $this->clinicService->delete($clinicId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function update($clinicId, StoreUpdateClinicRequest $request)
    {
        /** @var Clinic|null $item */
        $item = $this->clinicService->update($request->validated(), $clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }
}
