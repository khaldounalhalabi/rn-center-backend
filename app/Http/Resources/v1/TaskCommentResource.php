<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\TaskComment;

/** @mixin TaskComment */
class TaskCommentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'task_id' => $this->task_id,
            'comment' => $this->comment,
            'can_delete' => $this->canDelete(),
            'can_update' => $this->canUpdate(),
            'user' => new UserResource($this->whenLoaded('user')),
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
