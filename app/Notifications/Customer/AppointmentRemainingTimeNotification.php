<?php

namespace App\Notifications\Customer;

use App\Enums\NotificationResourceEnum;
use App\Models\Appointment;
use App\Modules\Notification\App\Notifications\BaseNotification;
use Carbon\Carbon;

class AppointmentRemainingTimeNotification extends BaseNotification
{
    /**
     * @param array{appointment:Appointment} $data
     */
    public function __construct(array $data = [])
    {
        $clinicName = $data['appointment']->clinic->user->full_name;
        $remainingEn = Carbon::parse($data['appointment']->date_time)->diffForHumans();
        Carbon::setLocale("ar");
        $remainingAr = Carbon::parse($data['appointment']->date_time)->diffForHumans();
        Carbon::setLocale("en");

        parent::__construct($data);
        $this->title('Your appointment is about to come')
            ->messageEn(trans('site.appointment_remaining_time', ['clinic_name' => $clinicName, 'remaining' => $remainingEn], "en"))
            ->messageAr(trans('site.appointment_remaining_time', ['clinic_name' => $clinicName, 'remaining' => $remainingAr], "ar"))
            ->data([
                'appointment_id' => $data['appointment']->id,
            ])->resource(NotificationResourceEnum::APPOINTMENT)
            ->resourceId($data['appointment']->id);
    }
}
