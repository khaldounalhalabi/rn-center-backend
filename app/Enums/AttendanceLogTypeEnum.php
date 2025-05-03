<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AttendanceLogTypeEnum: string
{
    use BaseEnum;

    case CHECKIN = "checkin";
    case CHECKOUT = "checkout";
}
