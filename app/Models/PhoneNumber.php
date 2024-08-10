<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @param null|string  $label
 * @param string       $phone
 * @param int          $phoneable_id
 * @param class-string $phoneable_type
 * @param null|string  $verification_code
 * @param bool         $is_verified
 */
class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'phone', 'phoneable_id', 'phoneable_type', 'verification_code', 'is_verified',
    ];

    protected $casts = [

    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'phone',
            'label',
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

    public function phoneable(): MorphTo
    {
        return $this->morphTo('phoneable');
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
}
