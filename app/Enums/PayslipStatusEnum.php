<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum PayslipStatusEnum: string
{
    use BaseEnum;

    case DRAFT = "draft";
    case EXCLUDED = "excluded";
    case DONE = "done";
    case FAILED = "failed";
}
