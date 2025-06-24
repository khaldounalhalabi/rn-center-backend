<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Task;

/** @mixin Task */
class TaskResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'label' => $this->label,
            'user_id' => $this->user_id,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'can_toggle_status' => $this->canChangeStatus(),
            'can_delete' => $this->canDelete(),
            'can_update' => $this->canUpdate(),
            'can_comment' => $this->canComment(),
            'task_comments' => TaskCommentResource::collection($this->whenLoaded('taskComments')),
            'issued_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
