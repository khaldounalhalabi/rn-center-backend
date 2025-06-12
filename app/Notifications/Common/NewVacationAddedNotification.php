<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class NewVacationAddedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->title("You've new vacation")
            ->messageEn(trans('site.you_ve_new_vacation', ['start_date' => $data['from'], 'end_date' => $data['to']], locale: 'en'))
            ->messageAr(trans('site.you_ve_new_vacation', ['start_date' => $data['from'], 'end_date' => $data['to']], locale: 'ar'))
            ->data([
                'vacation_id' => "{$data['vacation_id']}",
            ])->resource(NotificationResourceEnum::VACATION)
            ->resourceId($data['vacation_id']);
    }
}
