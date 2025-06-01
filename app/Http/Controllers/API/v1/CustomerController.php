<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Customer\StoreUpdateCustomerRequest;
use App\Http\Resources\v1\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerController extends ApiController
{
    private CustomerService $customerService;

    public function __construct()
    {
        $this->customerService = CustomerService::make();
        $this->relations = ['user', 'media'];
        $this->indexRelations = ['user'];
    }

    public function index()
    {
        $items = $this->customerService->indexWithPagination($this->indexRelations);
        if ($items) {
            return $this->apiResponse(CustomerResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($customerId)
    {
        /** @var Customer|null $item */
        $item = $this->customerService->view($customerId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(new CustomerResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateCustomerRequest $request)
    {
        /** @var Customer|null $item */
        $item = $this->customerService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new CustomerResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($customerId, StoreUpdateCustomerRequest $request)
    {
        /** @var Customer|null $item */
        $item = $this->customerService->update($request->validated(), $customerId, $this->relations);
        if ($item) {
            return $this->apiResponse(new CustomerResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($customerId)
    {
        $item = $this->customerService->delete($customerId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getRecent()
    {
        $data = $this->customerService->getRecent($this->indexRelations, ['validAppointments']);
        if ($data) {
            return $this->apiResponse(CustomerResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function pdfReport($customerId)
    {
        $data = $this->customerService->toPdf($customerId);
        if ($data) {
            return $data;
        }

        return $this->noData();
    }
}
