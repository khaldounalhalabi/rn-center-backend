<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Task\ChangeTaskStatusRequest;
use App\Http\Requests\v1\Task\StoreUpdateTaskRequest;
use App\Http\Resources\v1\TaskResource;
use App\Models\Task;
use App\Services\v1\Task\TaskService;

class TaskController extends ApiController
{
    private TaskService $taskService;

    public function __construct()
    {
        $this->taskService = TaskService::make();
        $this->relations = ['users', 'user', 'taskComments', 'taskComments.user'];
    }

    public function index()
    {
        $items = $this->taskService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(TaskResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($taskId)
    {
        /** @var Task|null $item */
        $item = $this->taskService->view($taskId, $this->relations);
        if ($item) {
            return $this->apiResponse(new TaskResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateTaskRequest $request)
    {
        /** @var Task|null $item */
        $item = $this->taskService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new TaskResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($taskId, StoreUpdateTaskRequest $request)
    {
        /** @var Task|null $item */
        $item = $this->taskService->update($request->validated(), $taskId, $this->relations);
        if ($item) {
            return $this->apiResponse(new TaskResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($taskId)
    {
        $item = $this->taskService->delete($taskId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function changeStatus(ChangeTaskStatusRequest $request)
    {
        $result = $this->taskService->changeStatus($request->validated());
        if ($result) {
            return $this->apiResponse(
                $result,
                self::STATUS_OK,
                trans('site.success')
            );
        }

        return $this->noData();
    }

    public function mine()
    {
        $data = $this->taskService->mine($this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(
                TaskResource::collection($data['data']),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
