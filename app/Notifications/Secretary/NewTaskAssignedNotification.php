<?php

namespace App\Notifications\Secretary;

use App\Enums\NotificationResourceEnum;
use App\Models\User;
use App\Modules\Notification\App\Notifications\BaseNotification;

class NewTaskAssignedNotification extends BaseNotification
{
    /**
     * @param array{user:User , task_id:int} $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title('New task added')
            ->messageEn(trans('site.new_task_assigned', ['username' => $data['user']->full_name], "en"))
            ->messageAr(trans('site.new_task_assigned', ['username' => $data['user']->full_name], "ar"))
            ->data([
                'task_id' => "{$data['task_id']}",
            ])->resource(NotificationResourceEnum::TASK)
            ->resourceId($data['task_id']);
    }
}
