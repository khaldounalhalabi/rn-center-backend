<?php

namespace App\Exceptions\Application;

class MoreThanOneAppointmentWithOneClinic extends ApplicationException
{
    public function __construct()
    {
        parent::__construct(
            "You cannot have more than one appointment with the same clinic in the same day",
            436
        );
    }
}
