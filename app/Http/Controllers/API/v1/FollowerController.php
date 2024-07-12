<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\FollowerResource;
use App\Services\FollowerService;

class FollowerController extends ApiController
{
    private FollowerService $followerService;

    public function __construct()
    {
        $this->followerService = FollowerService::make();

        // place the relations you want to return them within the response
        $this->relations = ['clinic'];
    }

    public function toggleFollow($clinicId)
    {
        $result = $this->followerService->toggleFollow($clinicId);
        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.success'));
        }
        return $this->noData();
    }

    public function getFollowedClinics()
    {
        $data = $this->followerService->getByCustomer(auth()->user()?->customer?->id, $this->relations, $this->countable);

        if ($data) {
            return $this->apiResponse(FollowerResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
