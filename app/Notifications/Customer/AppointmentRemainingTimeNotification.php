<?php

namespace App\Notifications\Customer;

use App\Notifications\BaseNotification;

class AppointmentRemainingTimeNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->setData($data);
        $this->setMessage($data['message']);
        $this->setType(AppointmentRemainingTimeNotification::class);
    }
}
