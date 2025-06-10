<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class VacationUpdatedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title("Your vacation has been updated")
            ->messageEn(trans('site.vacation_updated', locale: 'en'))
            ->messageAr(trans('site.vacation_updated', locale: 'ar'))
            ->data([
                'vacation_id' => $data['vacation_id'],
            ])->resource(NotificationResourceEnum::VACATION)
            ->resourceId($data['vacation_id']);
    }
}
