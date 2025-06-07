<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum VacationStatusEnum: string
{
    use BaseEnum;

    case APPROVED = "approved";
    case DRAFT = "draft";
    case REJECTED = "rejected";
}
