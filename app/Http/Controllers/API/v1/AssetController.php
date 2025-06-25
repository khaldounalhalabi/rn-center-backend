<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Asset\AssetCheckinRequest;
use App\Http\Requests\v1\Asset\AssetCheckoutRequest;
use App\Http\Requests\v1\Asset\StoreUpdateAssetRequest;
use App\Http\Resources\v1\AssetResource;
use App\Models\Asset;
use App\Services\AssetService;
use Exception;

class AssetController extends ApiController
{
    private AssetService $service;

    public function __construct()
    {
        $this->service = AssetService::make();
        $this->indexRelations = ['media', 'assignedUsers'];
        $this->relations = ['media', 'userAssets.user'];

        $this->countable = ['assignedUsers'];
    }

    public function index()
    {
        $items = $this->service->indexWithPagination($this->indexRelations, $this->countable);
        if ($items) {
            return $this->apiResponse(
                AssetResource::collection($items['data']),
                self::STATUS_OK,
                __('site.get_successfully'),
                $items['pagination_data']
            );
        }

        return $this->noData([]);
    }

    public function show($assetId)
    {
        /** @var Asset|null $item */
        $item = $this->service->view($assetId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(AssetResource::make($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdateAssetRequest $request)
    {
        /** @var Asset|null $item */
        $item = $this->service->store($request->validated(), $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(AssetResource::make($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    /**
     * @throws Exception
     */
    public function update($assetId, StoreUpdateAssetRequest $request)
    {
        /** @var Asset|null $item */
        $item = $this->service->update($request->validated(), $assetId, $this->relations, $this->countable);
        if ($item) {
            return $this->apiResponse(AssetResource::make($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function destroy($assetId)
    {
        $item = $this->service->delete($assetId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function checkin(AssetCheckinRequest $request)
    {
        $asset = $this->service->checkin($request->validated(), $this->relations, $this->countable);
        if ($asset) {
            return $this->apiResponse(AssetResource::make($asset), self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }

    public function checkout(AssetCheckoutRequest $request)
    {
        $asset = $this->service->checkout($request->validated(), $this->relations, $this->countable);
        if ($asset) {
            return $this->apiResponse(AssetResource::make($asset), self::STATUS_OK, __('site.success'));
        }

        return $this->noData();
    }
}
