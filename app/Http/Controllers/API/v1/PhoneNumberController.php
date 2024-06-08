<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\PhoneNumber\StoreUpdatePhoneNumberRequest;
use App\Http\Resources\PhoneNumberResource;
use App\Models\PhoneNumber;
use App\Services\PhoneNumberService;

class PhoneNumberController extends ApiController
{
    private PhoneNumberService $phoneNumberService;

    public function __construct()
    {

        $this->phoneNumberService = PhoneNumberService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->phoneNumberService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(PhoneNumberResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($phoneNumberId)
    {
        /** @var PhoneNumber|null $item */
        $item = $this->phoneNumberService->view($phoneNumberId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PhoneNumberResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdatePhoneNumberRequest $request)
    {
        /** @var PhoneNumber|null $item */
        $item = $this->phoneNumberService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new PhoneNumberResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($phoneNumberId, StoreUpdatePhoneNumberRequest $request)
    {
        /** @var PhoneNumber|null $item */
        $item = $this->phoneNumberService->update($request->validated(), $phoneNumberId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PhoneNumberResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($phoneNumberId)
    {
        $item = $this->phoneNumberService->delete($phoneNumberId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
