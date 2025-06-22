<?php

namespace App\Services\v1\TaskComment;

use App\Models\TaskComment;
use App\Models\User;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Common\NewCommentOnTaskNotification;
use App\Repositories\TaskCommentRepository;
use App\Repositories\TaskRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<TaskComment>
 * @property TaskCommentRepository $repository
 */
class TaskCommentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = TaskCommentRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $task = TaskRepository::make()->find($data['task_id']);
        if (!$task) {
            return null;
        }

        if (!$task->canComment()) {
            return null;
        }

        NotificationBuilder::make()
            ->data([
                'user' => user(),
                'task' => $task,
            ])->to(
                User::whereIn('id', [...$task->users->pluck('id')->toArray(), $task->user_id])
                    ->where('id', '!=', user()->id)
            )
            ->method(NotifyMethod::TO_QUERY)
            ->notification(NewCommentOnTaskNotification::class)
            ->send();

        return parent::store($data, $relationships, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $comment = $this->repository->find($id, ['task', 'task.users']);
        if (!$comment) {
            return null;
        }

        if (!$comment->canUpdate()) {
            return null;
        }

        NotificationBuilder::make()
            ->data([
                'user' => user(),
                'task' => $comment->task,
            ])->to(
                User::whereIn('id', [...$comment->task->users->pluck('id')->toArray(), $comment->task->user_id])
                    ->where('id', '!=', user()->id)
            )->method(NotifyMethod::TO_QUERY)
            ->notification(NewCommentOnTaskNotification::class)
            ->send();

        return $this->repository->update($data, $comment, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $comment = $this->repository->find($id);
        if (!$comment) {
            return null;
        }

        if (!$comment->canDelete()) {
            return null;
        }
        return $this->repository->delete($comment);
    }
}
