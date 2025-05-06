<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum PayslipAdjustmentTypeEnum: string
{
    use BaseEnum;

    case BENEFIT = "benefit";
    case DEDUCTION = "deduction";
}