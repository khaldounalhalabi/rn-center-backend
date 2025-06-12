<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class NewPayrunAddedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title("New payroll added")
            ->messageEn(trans('site.new_payroll_added', ['start_date' => $data['from'], 'end_date' => $data['to']], locale: 'en'))
            ->messageAr(trans('site.new_payroll_added', ['start_date' => $data['from'], 'end_date' => $data['to']], locale: 'ar'))
            ->data([
                'payrun_id' => "{$data['payrun_id']}",
            ])->resource(NotificationResourceEnum::PAYRUN)
            ->resourceId($data['payrun_id']);
    }
}
