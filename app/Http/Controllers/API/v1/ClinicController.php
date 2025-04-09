<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Clinic\StoreUpdateClinicRequest;
use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use App\Services\ClinicService;

class ClinicController extends ApiController
{
    private ClinicService $clinicService;

    public function __construct()
    {
        $this->clinicService = ClinicService::make();
        // place the relations you want to return them within the response

        if (!auth()->user() || auth()->user()?->isCustomer()) {
            $this->indexRelations = ['specialities', 'user', 'user.media',];
            $this->countable = [];
            $this->relations = ['media', 'user', 'specialities', 'user.media', 'schedules'];
        } else {
            $this->indexRelations = ['user'];
            $this->countable = ['appointments', 'todayAppointments', 'upcomingAppointments'];
            $this->relations = ['media', 'user', 'specialities', 'user.media'];
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

        return $this->noData(null);
    }

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

    public function getCurrentClinicAvailableTime()
    {
        if (!auth()->user()?->isClinic()) {
            return $this->noData();
        }

        $data = $this->clinicService->getClinicAvailableTimes(auth()->user()?->getClinicId());
        return $this->apiResponse($data, self::STATUS_OK, __('site.get_successfully'));
    }

    public function getClinicAvailableTimes($clinicId)
    {
        $data = $this->clinicService->getClinicAvailableTimes($clinicId);
        return $this->apiResponse($data, self::STATUS_OK, __('site.get_successfully'));
    }

    public function toggleClinicStatus($clinicId)
    {
        $data = $this->clinicService->toggleClinicStatus($clinicId);

        if ($data) {
            return $this->apiResponse($data, self::STATUS_OK, __('site.success'));
        }
        return $this->noData();
    }

    public function updateDoctorClinic(StoreUpdateClinicRequest $request)
    {
        $clinicId = auth()->user()?->getClinicId();
        if (!$clinicId) {
            return $this->noData();
        }

        $item = $this->clinicService->update($request->validated(), $clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function update($clinicId, StoreUpdateClinicRequest $request)
    {
        /** @var Clinic|null $item */
        $item = $this->clinicService->update($request->validated(), $clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function showDoctorClinic()
    {
        $clinicId = auth()->user()?->getClinicId();
        if (!$clinicId) {
            return $this->noData();
        }

        $item = $this->clinicService->view($clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function agreeOnContract()
    {
        $result = auth()?->user()?->clinic?->update(['agreed_on_contract' => true]);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.success'));
        }

        return $this->noData(false);
    }

    public function getOnlineBySpeciality($specialityId)
    {
        $data = $this->clinicService->getOnlineBySpecialityId($specialityId, $this->indexRelations, $this->countable);
        if ($data) {
            return $this->apiResponse(ClinicResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
