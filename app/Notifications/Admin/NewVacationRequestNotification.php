<?php

namespace App\Notifications\Admin;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;
use App\Repositories\UserRepository;

class NewVacationRequestNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $user = UserRepository::make()->find($data['user_id']);
        $this->title('New vacation request')
            ->messageEn(trans('site.vacation_request', ['user_name' => $user->full_name, 'start_date' => $data['start_date'], 'end_date' => $data['end_date']], locale: 'en'))
            ->messageAr(trans('site.vacation_request', ['user_name' => $user->full_name, 'start_date' => $data['start_date'], 'end_date' => $data['end_date']], locale: 'en'))
            ->data([
                'vacation_id' => "{$data['vacation_id']}",
            ])->resource(NotificationResourceEnum::VACATION)
            ->resourceId($data['vacation_id']);
    }
}
