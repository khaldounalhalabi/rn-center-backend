<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Address\StoreUpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\Address\IAddressService;

class AddressController extends ApiController
{
    private IAddressService $addressService;

    public function __construct(IAddressService $addressService)
    {

        $this->addressService = $addressService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->addressService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(AddressResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($addressId)
    {
        /** @var Address|null $item */
        $item = $this->addressService->view($addressId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AddressResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateAddressRequest $request)
    {
        /** @var Address|null $item */
        $item = $this->addressService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new AddressResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($addressId, StoreUpdateAddressRequest $request)
    {
        /** @var Address|null $item */
        $item = $this->addressService->update($request->validated(), $addressId, $this->relations);
        if ($item) {
            return $this->apiResponse(new AddressResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($addressId)
    {
        $item = $this->addressService->delete($addressId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
