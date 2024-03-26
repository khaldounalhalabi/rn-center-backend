<?php

namespace App\Models;

use App\Casts\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string name
 * @property string city
 * @property string lat
 * @property string lng
 * @property string country
 * @property numeric addressable_id
 * @property string addressable_type
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'lat',
        'lng',
        'country',
        'addressable_id',
        'addressable_type',
        'city_id',
    ];

    protected $casts = [
        'name' => Translatable::class,
        'city' => Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'city',
            'lat',
            'lng',
            'country',
            'addressable_type',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [

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
            //filesKeys
        ];
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
