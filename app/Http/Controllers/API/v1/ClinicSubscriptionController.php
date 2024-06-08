<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ClinicSubscription\StoreUpdateClinicSubscriptionRequest;
use App\Http\Resources\ClinicSubscriptionResource;
use App\Models\ClinicSubscription;
use App\Services\ClinicSubscriptionService;

class ClinicSubscriptionController extends ApiController
{
    private ClinicSubscriptionService $clinicSubscriptionService;

    public function __construct()
    {

        $this->clinicSubscriptionService = ClinicSubscriptionService::make();

        // place the relations you want to return them within the response
        $this->relations = ['subscription', 'clinic.user'];
    }

    public function show($clinicSubscriptionId)
    {
        /** @var ClinicSubscription|null $item */
        $item = $this->clinicSubscriptionService->view($clinicSubscriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicSubscriptionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateClinicSubscriptionRequest $request)
    {
        /** @var ClinicSubscription|null $item */
        $item = $this->clinicSubscriptionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicSubscriptionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($clinicSubscriptionId, StoreUpdateClinicSubscriptionRequest $request)
    {
        /** @var ClinicSubscription|null $item */
        $item = $this->clinicSubscriptionService->update($request->validated(), $clinicSubscriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicSubscriptionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicSubscriptionId)
    {
        $item = $this->clinicSubscriptionService->delete($clinicSubscriptionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getByClinic($clinicId)
    {
        $data = $this->clinicSubscriptionService->getClinicSubscriptions($clinicId, $this->relations);

        if ($data) {
            return $this->apiResponse(ClinicSubscriptionResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData([]);
    }
}
