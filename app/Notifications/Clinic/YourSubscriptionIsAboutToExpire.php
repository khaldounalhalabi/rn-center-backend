<?php

namespace App\Notifications\Clinic;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class YourSubscriptionIsAboutToExpire extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $leftDays = $data['left_days'];
        $this->setData([
            'left_days' => $leftDays,
        ]);
        $this->setMessage("Your subscription is about to expire , you have $leftDays days left");
        $this->setMessageAR(" إن اشتراكك اقترب على الانتهاء , لديك $leftDays أيام متبقية في اشتراكك ");
        $this->setType(YourSubscriptionIsAboutToExpire::class);

    }
}
