<?php

namespace App\Notifications\Common;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Notifications\BaseNotification;

class AppointmentEventNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $event = strtolower($data['event']);
        $this->title('Appointment has been ' . $event)
            ->messageEn(trans('site.appointment_event', ['event' => trans('site.' . $event, locale: 'en')], locale: 'en'))
            ->messageAr(trans('site.appointment_event', ['event' => trans('site.' . $event, locale: "ar")], locale: "ar"))
            ->data([
                'event' => $data['event'],
                'appointment' => $data['appointment'],
            ])->resource(NotificationResourceEnum::APPOINTMENT)
            ->resourceId($data['appointment']->id);
    }
}
