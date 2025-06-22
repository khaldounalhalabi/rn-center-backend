<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum TaskStatusEnum: string
{
    use BaseEnum;

    case PENDING = "pending";
    case COMPLETED = "completed";
    case CANCELLED = "cancelled";
    case IN_PROGRESS = "in_progress";
}
