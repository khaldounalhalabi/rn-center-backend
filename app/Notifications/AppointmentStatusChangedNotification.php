<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentStatusChangedNotification extends BaseNotification
{
    /**
     * @param array{appointment:Appointment} $data
     */
    public function __construct(array $data)
    {
        $appointment = $data['appointment'];
        $newData['url'] = route('api.admin.appointments.show', $appointment->id);
        $newData['appointment_id'] = $appointment->id;
        $newData['user_id'] = $appointment->customer_id;

        parent::__construct($newData);
        $this->setData($newData);
        $this->setMessage("Your Appointment Booked In " . $appointment->date->format('Y-m-d') . " In " . $appointment->clinic->name . " Clinic Has Been CHanged To " . $appointment->status);
    }
}
