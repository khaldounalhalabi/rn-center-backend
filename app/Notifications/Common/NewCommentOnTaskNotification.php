<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Models\Task;
use App\Models\User;
use App\Modules\Notification\App\Notifications\BaseNotification;

class NewCommentOnTaskNotification extends BaseNotification
{
    /**
     * @param array{user:User , task:Task} $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title('New comment on task')
            ->messageEn(trans(
                'site.new_comment_on_task',
                [
                    'username' => $data['user']->full_name,
                    'task_title' => $data['task']->title,
                ],
                'en'
            ))
            ->messageAr(trans(
                'site.new_comment_on_task',
                [
                    'username' => $data['user']->full_name,
                    'task_title' => $data['task']->title,
                ],
                'ar'
            ))
            ->data([
                'task_id' => "{$data['task']->id}",
            ])->resource(NotificationResourceEnum::TASK)
            ->resourceId($data['task']->id);
    }
}
