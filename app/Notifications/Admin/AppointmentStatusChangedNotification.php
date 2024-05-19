<?php

namespace App\Notifications\Admin;

use App\Models\Appointment;
use App\Notifications\BaseNotification;

class AppointmentStatusChangedNotification extends BaseNotification
{
    /**
     * @param array{appointment:Appointment} $data
     */
    public function __construct(array $data)
    {
        $appointment = $data['appointment'];
        $newData['appointment_id'] = $appointment->id;
        $newData['user_id'] = $appointment->customer_id;
        $newData['status'] = $appointment->status;

        parent::__construct($newData);
        $this->setData($newData);
        $this->setMessage("Your Appointment Booked In " . $appointment->date->format('Y-m-d') . " In " . $appointment->clinic->name->en . " Clinic Has Been Changed To " . $appointment->status);
        $this->setType(AppointmentStatusChangedNotification::class);
    }
}
