<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Offer\StoreUpdateOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Services\OfferService;

class OfferController extends ApiController
{
    private OfferService $offerService;

    public function __construct()
    {
        $this->offerService = OfferService::make();
        // place the relations you want to return them within the response
        $this->relations = ['clinic', 'media'];
    }

    public function index()
    {
        $items = $this->offerService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(OfferResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($offerId)
    {
        /** @var Offer|null $item */
        $item = $this->offerService->view($offerId, $this->relations);
        if ($item) {
            return $this->apiResponse(new OfferResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdateOfferRequest $request)
    {
        /** @var Offer|null $item */
        $item = $this->offerService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new OfferResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($offerId, StoreUpdateOfferRequest $request)
    {
        /** @var Offer|null $item */
        $item = $this->offerService->update($request->validated(), $offerId, $this->relations);
        if ($item) {
            return $this->apiResponse(new OfferResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($offerId)
    {
        $item = $this->offerService->delete($offerId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getByClinic($clinicId)
    {
        $data = $this->offerService->getByClinic($clinicId, $this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(OfferResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
