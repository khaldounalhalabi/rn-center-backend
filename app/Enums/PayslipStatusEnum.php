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
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public static function forEmployees(): array
    {
        return [
            self::ACCEPTED->value,
            self::REJECTED->value,
            self::DRAFT->value,
        ];
    }
}
