<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\UserAssetResource;
use App\Services\UserAssetService;

/**
 * @property UserAssetService service
 */
class UserAssetController extends ApiController
{
    private UserAssetService $service;

    public function __construct()
    {
        $this->service = UserAssetService::make();
        $this->relations = [];
    }

    public function getByUser($userId)
    {
        $data = $this->service->getByUser($userId, ['asset']);
        if ($data) {
            return $this->apiResponse(
                UserAssetResource::collection($data['data']),
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData();
    }

    public function getByAsset($assetId)
    {
        $data = $this->service->getByAsset($assetId, ['user']);

        if ($data) {
            return $this->apiResponse(
                UserAssetResource::collection($data['data']),
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData();
    }

    public function assignedToMe()
    {
        $data = $this->service->getByUser(user()->id, ['asset']);
        if ($data) {
            return $this->apiResponse(
                UserAssetResource::collection($data['data']),
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
