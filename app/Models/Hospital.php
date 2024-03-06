<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property Address addresses
 * @property string name
 * @property string phone_numbers
 * @property string available_departments
 * @property string images
 */
class Hospital extends Model implements HasMedia
{
    use HasFactory;
    use Translations;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'addresses' => [
                //add your addresses desired column to be search within
            ],

        ];
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'images' => ["type" => MediaTypeEnum::MULTIPLE->value],
            //filesKeys
        ];
    }


}
