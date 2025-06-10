<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Enums\VacationStatusEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class VacationStatusChangedNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $title = $data['status'] == VacationStatusEnum::APPROVED->value
            ? 'Your vacation has been approved'
            : ($data['status'] == VacationStatusEnum::REJECTED->value
                ? "Your vacation has been rejected"
                : "Your vacation is still a draft");

        $message = $data['status'] == VacationStatusEnum::APPROVED->value
            ? 'site.vacation_approved'
            : ($data['status'] == VacationStatusEnum::REJECTED->value
                ? 'site.vacation_rejected'
                : 'site.vacation_drafted');

        $this->title($title)
            ->messageEn(trans($message, locale: 'en'))
            ->messageAr(trans($message, locale: 'ar'))
            ->data([
                'vacation_id' => $data['vacation_id'],
                'status' => $data['status'],
            ])->resource(NotificationResourceEnum::VACATION)
            ->resourceId($data['vacation_id']);
    }
}
