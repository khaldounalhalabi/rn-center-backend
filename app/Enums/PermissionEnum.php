<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum PermissionEnum: string
{
    use BaseEnum;

    case HOLIDAYS_MANAGEMENT = "holidays management";
    case ATTENDANCE_MANAGEMENT = "attendance_management";
}
