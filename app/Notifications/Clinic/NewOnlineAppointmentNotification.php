<?php

namespace App\Notifications\Clinic;

use App\Models\Appointment;
use App\Notifications\BaseNotification;

class NewOnlineAppointmentNotification extends BaseNotification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        /** @var Appointment $appointment */
        parent::__construct($data);
        $appointment = $data['appointment'];
        $this->setData([
            'appointment_id' => $appointment->id,
        ]);
        $this->setMessageAR("لديك موعد جديد في {$appointment->date->format('Y-m-d')}");
        $this->setMessage("You have new online appointment at {$appointment->date->format('Y-m-d')}");
        $this->setType(NewOnlineAppointmentNotification::class);
        $this->setTitle("You have new appointment");
    }
}
