<?php

namespace App\Notifications\RealTime;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceChangeNotification extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->setData($data);
        $this->setType(BalanceChangeNotification::class);
    }
}
