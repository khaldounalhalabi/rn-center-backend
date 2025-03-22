<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\User\StoreUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends ApiController
{
    private UserService $userService;

    public function __construct()
    {

        $this->userService = UserService::make();

        // place the relations you want to return them within the response
        $this->relations = ['media', 'roles', 'address.city', 'phones'];
    }

    public function index()
    {
        $items = $this->userService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(UserResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($userId)
    {
        $item = $this->userService->view($userId, $this->relations);
        if ($item) {
            return $this->apiResponse(new UserResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateUserRequest $request)
    {
        /** @var User|null $item */
        $item = $this->userService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new UserResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($userId, StoreUpdateUserRequest $request)
    {
        /** @var User|null $item */
        $item = $this->userService->update($request->validated(), $userId, $this->relations);
        if ($item) {
            return $this->apiResponse(new UserResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($userId)
    {
        $item = $this->userService->delete($userId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
