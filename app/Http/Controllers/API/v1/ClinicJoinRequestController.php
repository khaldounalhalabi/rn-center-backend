<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\ClinicJoinRequest\StoreUpdateClinicJoinRequestRequest;
use App\Http\Resources\v1\ClinicJoinRequestResource;
use App\Models\ClinicJoinRequest;
use App\Services\v1\ClinicJoinRequest\ClinicJoinRequestService;
use Illuminate\Http\Request;

class ClinicJoinRequestController extends ApiController
{
    private ClinicJoinRequestService $clinicJoinRequestService;

    public function __construct()
    {
        $this->clinicJoinRequestService = ClinicJoinRequestService::make();

        // place the relations you want to return them within the response
        $this->relations = ['city'];
    }

    public function index()
    {
        $items = $this->clinicJoinRequestService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ClinicJoinRequestResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($clinicJoinRequestId)
    {
        /** @var ClinicJoinRequest|null $item */
        $item = $this->clinicJoinRequestService->view($clinicJoinRequestId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicJoinRequestResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateClinicJoinRequestRequest $request)
    {
        /** @var ClinicJoinRequest|null $item */
        $item = $this->clinicJoinRequestService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicJoinRequestResource($item), self::STATUS_OK, __('site.join_request_success'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($clinicJoinRequestId, StoreUpdateClinicJoinRequestRequest $request)
    {
        /** @var ClinicJoinRequest|null $item */
        $item = $this->clinicJoinRequestService->update($request->validated(), $clinicJoinRequestId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicJoinRequestResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicJoinRequestId)
    {
        $item = $this->clinicJoinRequestService->delete($clinicJoinRequestId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->clinicJoinRequestService->export($ids);
    }

    public function getImportExample()
    {
        return $this->clinicJoinRequestService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->clinicJoinRequestService->import();
    }
}
