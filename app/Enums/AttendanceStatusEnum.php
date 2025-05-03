<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AttendanceStatusEnum: string
{
    use BaseEnum;

    case APPROVED = "approved";
    case DRAFT = "draft";
}
