<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum GenderEnum: string
{
    use BaseEnum;

    case MALE = 'male';
    case FEMALE = 'female';
}
