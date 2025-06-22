<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum TaskLabelEnum: string
{
    use BaseEnum;

    case URGENT = "urgent";
    case NORMAL = "normal";
    case LOW = "low";
}
