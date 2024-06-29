<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\BloodDonationRequest\StoreUpdateBloodDonationRequestRequest;
use App\Http\Resources\BloodDonationRequestResource;
use App\Models\BloodDonationRequest;
use App\Services\BloodDonationRequestService;
use Illuminate\Http\Request;

class BloodDonationRequestController extends ApiController
{
    private BloodDonationRequestService $bloodDonationRequestService;

    public function __construct()
    {
        $this->bloodDonationRequestService = BloodDonationRequestService::make();
        $this->relations = ['city'];
    }

    public function index()
    {
        $items = $this->bloodDonationRequestService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(BloodDonationRequestResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($bloodDonationRequestId)
    {
        /** @var BloodDonationRequest|null $item */
        $item = $this->bloodDonationRequestService->view($bloodDonationRequestId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BloodDonationRequestResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateBloodDonationRequestRequest $request)
    {
        /** @var BloodDonationRequest|null $item */
        $item = $this->bloodDonationRequestService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new BloodDonationRequestResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($bloodDonationRequestId, StoreUpdateBloodDonationRequestRequest $request)
    {
        /** @var BloodDonationRequest|null $item */
        $item = $this->bloodDonationRequestService->update($request->validated(), $bloodDonationRequestId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BloodDonationRequestResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($bloodDonationRequestId)
    {
        $item = $this->bloodDonationRequestService->delete($bloodDonationRequestId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->bloodDonationRequestService->export($ids);
    }

    public function getImportExample()
    {
        return $this->bloodDonationRequestService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->bloodDonationRequestService->import();
    }
}
