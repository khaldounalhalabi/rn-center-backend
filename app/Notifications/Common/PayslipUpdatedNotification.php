<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class PayslipUpdatedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->title("Your payslip has been updated")
            ->messageEn(trans('site.payslip_updated', locale: 'en'))
            ->messageAr(trans('site.payslip_updated', locale: 'ar'))
            ->data([
                'payslip_id' => "{$data['payslip_id']}",
            ])->resource(NotificationResourceEnum::PAYSLIP)
            ->resourceId($data['payslip_id']);
    }
}
