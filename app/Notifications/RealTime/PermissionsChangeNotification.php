<?php

namespace App\Notifications\RealTime;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class PermissionsChangeNotification extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->setType(PermissionsChangeNotification::class);
    }
}
