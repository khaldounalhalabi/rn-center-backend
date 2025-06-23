<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AssetStatusEnum: string
{
    use BaseEnum;

    case CHECKIN = 'checkin';
    case CHECKOUT = 'checkout';
}
