<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\TaskComment\StoreUpdateTaskCommentRequest;
use App\Http\Resources\v1\TaskCommentResource;
use App\Models\TaskComment;
use App\Services\v1\TaskComment\TaskCommentService;

class TaskCommentController extends ApiController
{
    private TaskCommentService $taskCommentService;

    public function __construct()
    {
        $this->taskCommentService = TaskCommentService::make();
        $this->relations = [];
    }

    public function store(StoreUpdateTaskCommentRequest $request)
    {
        /** @var TaskComment|null $item */
        $item = $this->taskCommentService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new TaskCommentResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($taskCommentId, StoreUpdateTaskCommentRequest $request)
    {
        /** @var TaskComment|null $item */
        $item = $this->taskCommentService->update($request->validated(), $taskCommentId, $this->relations);
        if ($item) {
            return $this->apiResponse(new TaskCommentResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($taskCommentId)
    {
        $item = $this->taskCommentService->delete($taskCommentId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
