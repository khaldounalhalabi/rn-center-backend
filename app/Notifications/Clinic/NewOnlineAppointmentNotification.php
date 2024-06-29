<?php

namespace App\Notifications\Clinic;

use App\Notifications\BaseNotification;

class NewOnlineAppointmentNotification extends BaseNotification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->setData($data);
        $this->setMessage($data['message']);
        $this->setType(NewOnlineAppointmentNotification::class);
    }
}
