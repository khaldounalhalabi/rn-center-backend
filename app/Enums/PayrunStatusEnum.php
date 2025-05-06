<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum PayrunStatusEnum: string
{
    use BaseEnum;

    case DRAFT = "draft";
    case APPROVED = "approved";
    case DONE = "done";
    case PROCESSING = "processing";
}
