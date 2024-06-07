<?php

namespace App\Notifications\RealTime;

use App\Models\Appointment;
use App\Notifications\BaseNotification;

class AppointmentStatusChangeNotification extends BaseNotification
{
    /**
     * @param array{appointment:Appointment} $data
     */
    public function __construct(array $data)
    {
        $appointment = $data['appointment'];
        $newData['appointment_id'] = $appointment->id;
        $newData['status'] = $appointment->status;

        parent::__construct($newData);
        $this->setData($newData);
        $this->setType(AppointmentStatusChangeNotification::class);
    }
}
