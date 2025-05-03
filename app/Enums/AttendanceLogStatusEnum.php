<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AttendanceLogStatusEnum: string
{
    use BaseEnum;

    case ON_TIME = "on_time";
    case LATE = "late";
    case EARLY_LEAVE = "early_leave";
    case OVER_TIME = "over_time";
}
