<?php

namespace App\Notifications\Admin;

use App\Enums\NotificationResourceEnum;
use App\Enums\PayslipStatusEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class PayslipStatusChangedNotification extends BaseNotification
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $title = $data['status'] == PayslipStatusEnum::ACCEPTED->value
            ? "Payslip has been accepted"
            : ($data['status'] == PayslipStatusEnum::REJECTED->value
                ? "Payslip has been rejected"
                : "Payslip is still draft");

        $message = $data['status'] == PayslipStatusEnum::ACCEPTED->value
            ? "site.payslip_accepted"
            : ($data['status'] == PayslipStatusEnum::REJECTED->value
                ? "site.payslip_rejected"
                : "site.payslip_drafted");

        $this->title($title)
            ->messageEn(trans($message, ['user_name' => $data['user_name']], locale: 'en'))
            ->messageAr(trans($message, ['user_name' => $data['user_name']], locale: 'ar'))
            ->data([
                'payslip_id' => "{$data['payslip_id']}",
                'status' => "{$data['status']}",
                'payrun_id' => "{$data['payrun_id']}",
            ])->resource(NotificationResourceEnum::PAYSLIP)
            ->resourceId($data['payslip_id']);
    }
}
