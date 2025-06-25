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

    public function getAssignedByUser($userId)
    {
        $data = $this->service->getAssignedByUser($userId, ['asset']);
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

    public function getAssignedByAsset($assetId)
    {
        $data = $this->service->getAssignedByAsset($assetId, ['user']);

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
        $data = $this->service->getAssignedByUser(user()->id, ['asset']);
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
