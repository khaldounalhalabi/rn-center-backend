<?php

namespace App\Services\v1\Task;

use App\Enums\NotificationResourceEnum;
use App\Models\Task;
use App\Models\User;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Modules\Notification\App\Services\NotificationService;
use App\Notifications\Secretary\NewTaskAssignedNotification;
use App\Repositories\TaskRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Task>
 * @property TaskRepository $repository
 */
class TaskService extends BaseService
{
    use Makable;

    protected string $repositoryClass = TaskRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $task = $this->repository->create($data);
        if (!$task) {
            return null;
        }

        if (isset($data['users'])) {
            $task->users()->sync($data['users']);
            NotificationBuilder::make()
                ->data([
                    'user' => user(),
                    'task_id' => $task->id,
                ])->method(NotifyMethod::TO_QUERY)
                ->to(User::whereIn('id', $data['users']))
                ->notification(NewTaskAssignedNotification::class)
                ->send();
        }

        return $task->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $task = $this->repository->find($id, ['users']);

        if (!$task) {
            return null;
        }

        $task = $this->repository->update($data, $task);

        if (isset($data['users'])) {
            $prevIds = $task->users->pluck('id')->toArray();
            $task->users()->sync($data['users']);
            $newIds = array_values(array_diff($data['users'], $prevIds));
            NotificationBuilder::make()
                ->data([
                    'user' => user(),
                    'task_id' => $task->id,
                ])->method(NotifyMethod::TO_QUERY)
                ->to(User::whereIn('id', $newIds))
                ->notification(NewTaskAssignedNotification::class)
                ->send();

            NotificationService::make()->deleteByNotifiableAndResource(
                array_values(array_diff($prevIds, $data['users'])),
                $task->id,
                NotificationResourceEnum::TASK
            );
        }

        return $task->load($relationships)->loadCount($countable);
    }

    public function delete($id): ?bool
    {
        $task = $this->repository->find($id, ['users']);
        if (!$task) {
            return null;
        }

        NotificationService::make()->deleteByNotifiableAndResource(
            $task->users->pluck('id')->toArray(),
            $task->id,
            NotificationResourceEnum::TASK
        );

        return $task->delete();
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function changeStatus(array $data): ?string
    {
        $task = $this->repository->find($data['task_id']);
        if (!$task) {
            return null;
        }

        if ($data['status'] == $task->status) {
            return $data['status'];
        }

        $task = $this->repository->update([
            'status' => $data['status'],
        ], $task);

        NotificationBuilder::make()
            ->data([
                'user' => user()->id,
                'task' => $task,
                'status' => $data['status']
            ]);

        return $data['status'];
    }

    public function mine(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByUser(user()->id, $relations, $countable);
    }
}
