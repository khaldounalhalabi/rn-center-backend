<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Clinic\StoreUpdateClinicRequest;
use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use App\Services\Clinic\IClinicService;

class ClinicController extends ApiController
{
    private IClinicService $clinicService;

    public function __construct(IClinicService $clinicService)
    {
        $this->clinicService = $clinicService;
        // place the relations you want to return them within the response

        $this->relations = ['user', 'user.address', "user.address.city", 'user.phones', 'specialities', 'hospital', 'user.media'];
        $this->indexRelations = ['user', 'user.phones', 'user.address', 'user.address.city'];
        $this->countable = ['appointments', 'todayAppointments', 'upcomingAppointments'];
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

    public function update($clinicId, StoreUpdateClinicRequest $request)
    {
        /** @var Clinic|null $item */
        $item = $this->clinicService->update($request->validated(), $clinicId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new ClinicResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicId)
    {
        $item = $this->clinicService->delete($clinicId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getClinicAvailableTimes($clinicId)
    {
        $data = $this->clinicService->getClinicAvailableTimes($clinicId);
        return $this->apiResponse($data, self::STATUS_OK, __('site.get_successfully'));
    }
}
