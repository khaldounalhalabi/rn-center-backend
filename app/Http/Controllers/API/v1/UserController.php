<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\User\StoreUpdateUserRequest;
use App\Http\Resources\v1\AttendanceResource;
use App\Http\Resources\v1\UserResource;
use App\Services\UserService;

class UserController extends ApiController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = UserService::make();
        $this->relations = ['roles', 'formula'];
    }

    public function addSecretary(StoreUpdateUserRequest $request)
    {
        $data = $request->validated();
        $data['role'] = RolesPermissionEnum::SECRETARY['role'];
        $user = $this->userService->store($data, $this->relations, $this->countable);
        $this->userService->sendVerificationCode($user);
        return $this->apiResponse(UserResource::make($user), self::STATUS_OK, __('site.stored_successfully'));
    }

    public function update(StoreUpdateUserRequest $request, $userId)
    {
        $user = $this->userService->update($request->validated(), $userId, $this->relations, $this->countable);
        if ($user) {
            return $this->apiResponse(UserResource::make($user), self::STATUS_OK, __('site.update_successfully'));
        }
        return $this->noData();
    }

    public function destroy($userId)
    {
        $result = $this->userService->delete($userId);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, trans('site.delete_successfully'));
        }

        return $this->noData();
    }

    public function show($userId)
    {
        $user = $this->userService->view($userId, $this->relations, $this->countable);
        if ($user) {
            return $this->apiResponse(UserResource::make($user), self::STATUS_OK, __('site.get_successfully'));
        }
        return $this->noData();
    }

    public function secretaries()
    {
        $data = $this->userService->getSecretaries($this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(UserResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }


    public function allWithAttendanceByDate()
    {
        $data = $this->userService->getWithAttendance([
            'clinic',
            'roles'
        ]);
        if ($data && isset($data['users']['data'], $data['users']['pagination_data'])) {
            return $this->apiResponse(
                [
                    'attendance' => AttendanceResource::make($data['attendance']),
                    'users' => UserResource::collection($data['users']['data'])
                ],
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['users']['pagination_data'],
            );
        }

        return $this->noData();
    }

    public function employees()
    {
        $data = $this->userService->employees($this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(
                UserResource::collection($data['data']),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
