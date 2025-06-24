<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Models\Task;
use App\Models\User;
use App\Modules\Notification\App\Notifications\BaseNotification;

class TaskStatusChangedNotification extends BaseNotification
{
    /**
     * @param array{user:User , task:Task , status:string} $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title('Task status changed')
            ->messageEn(trans(
                'site.task_status_changed',
                [
                    'username' => $data['user']->full_name,
                    'task_title' => $data['task']->title,
                    'status' => trans("site.{$data['status']}", locale: "en")
                ],
                'en'
            ))
            ->messageAr(trans(
                'site.task_status_changed',
                [
                    'username' => $data['user']->full_name,
                    'task_title' => $data['task']->title,
                    'status' => trans("site.{$data['status']}", locale: "ar")
                ],
                'ar'
            ))
            ->data([
                'task_id' => "{$data['task']->id}",
            ])->resource(NotificationResourceEnum::TASK)
            ->resourceId($data['task']->id);
    }
}
