<?php

namespace App\Notifications\RealTime;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class NewAppointmentNotification extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}
