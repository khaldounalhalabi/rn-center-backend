<?php

namespace App\Notifications\Customer;

use App\Models\Appointment;
use App\Notifications\BaseNotification;

class CustomerAppointmentChangedNotification extends BaseNotification
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
        $this->setMessageAR($appointment->status . "تغيرت حالته إلى " . $appointment->clinic->name->ar . "عند عيادة" . $appointment->date->format('Y-m-d') . "موعدك المحجوز في تاريخ");
        $this->setType(CustomerAppointmentChangedNotification::class);
    }
}
