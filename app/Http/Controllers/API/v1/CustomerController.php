<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Customer\DoctorStoreUpdateCustomerRequest;
use App\Http\Requests\Customer\StoreUpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerController extends ApiController
{
    private CustomerService $customerService;

    public function __construct()
    {

        $this->customerService = CustomerService::make();

        if (auth()->user()?->isDoctor()) {
            $this->relations = [
                'currentClinicPatientProfile.media',
                'user.address.city',
                'user.phones',
                'user.media',
                'currentClinicPatientProfile',
                'user'
            ];
        } else {
            $this->relations = [
                'user',
                'user.address',
                'user.address.city',
                'user.phones',
                'user.media'
            ];
        }
    }

    public function index()
    {
        $items = $this->customerService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(CustomerResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($customerId)
    {
        /** @var Customer|null $item */
        $item = $this->customerService->view($customerId, $this->relations);
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

    public function getDoctorCustomers()
    {
        $data = $this->customerService->getDoctorCustomers([
            'user', 'user.address', 'user.address.city', 'user.phones'
        ]);

        if ($data) {
            return $this->apiResponse(CustomerResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function doctorAddCustomer(DoctorStoreUpdateCustomerRequest $request)
    {
        $data = $this->customerService->doctorAddCustomer($request->validated(), $this->relations);

        if ($data) {
            return $this->apiResponse(new CustomerResource($data), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->noData();
    }

    public function doctorUpdateCustomer(DoctorStoreUpdateCustomerRequest $request, $customerId)
    {
        $data = $this->customerService->doctorUpdateCustomer($customerId, $request->validated(), $this->relations);

        if ($data) {
            return $this->apiResponse(new CustomerResource($data), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function doctorDeleteCustomer($customerId)
    {
        $result = $this->customerService->doctorDeleteCustomer($customerId);

        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData();
    }
}
