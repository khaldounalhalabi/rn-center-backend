<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Subscription\StoreUpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends ApiController
{
    private SubscriptionService $subscriptionService;

    public function __construct()
    {

        $this->subscriptionService = SubscriptionService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->subscriptionService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(SubscriptionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($subscriptionId)
    {
        /** @var Subscription|null $item */
        $item = $this->subscriptionService->view($subscriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SubscriptionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdateSubscriptionRequest $request)
    {
        /** @var Subscription|null $item */
        $item = $this->subscriptionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new SubscriptionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($subscriptionId, StoreUpdateSubscriptionRequest $request)
    {
        /** @var Subscription|null $item */
        $item = $this->subscriptionService->update($request->validated(), $subscriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SubscriptionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($subscriptionId)
    {
        $item = $this->subscriptionService->delete($subscriptionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->subscriptionService->export($ids);
    }

    public function getImportExample()
    {
        return $this->subscriptionService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->subscriptionService->import();
    }
}
