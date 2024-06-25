<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\SystemOffer\StoreUpdateSystemOfferRequest;
use App\Http\Resources\SystemOfferResource;
use App\Models\SystemOffer;
use App\Services\SystemOffer\SystemOfferService;
use Illuminate\Http\Request;

class SystemOfferController extends ApiController
{
    private SystemOfferService $systemOfferService;

    public function __construct()
    {
        $this->systemOfferService = SystemOfferService::make();
        // place the relations you want to return them within the response
        $this->relations = ['media'];
    }

    public function index()
    {
        $items = $this->systemOfferService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(SystemOfferResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($systemOfferId)
    {
        /** @var SystemOffer|null $item */
        $item = $this->systemOfferService->view($systemOfferId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SystemOfferResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateSystemOfferRequest $request)
    {
        /** @var SystemOffer|null $item */
        $item = $this->systemOfferService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new SystemOfferResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($systemOfferId, StoreUpdateSystemOfferRequest $request)
    {
        /** @var SystemOffer|null $item */
        $item = $this->systemOfferService->update($request->validated(), $systemOfferId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SystemOfferResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($systemOfferId)
    {
        $item = $this->systemOfferService->delete($systemOfferId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->systemOfferService->export($ids);
    }

    public function getImportExample()
    {
        return $this->systemOfferService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->systemOfferService->import();
    }
}
