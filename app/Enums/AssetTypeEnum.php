<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum AssetTypeEnum: string
{
    use BaseEnum;

    case ASSET = "asset";
    case ACCESSORIES = "accessories";
    case CONSUMABLE = "consumable";

    public static function needQuantity(): array
    {
        return [
            self::ACCESSORIES->value,
            self::CONSUMABLE->value,
        ];
    }

    public static function needCheckout(): array
    {
        return [
            self::ACCESSORIES->value,
            self::ASSET->value,
        ];
    }
}
