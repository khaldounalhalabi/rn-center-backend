<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string phone
 * @property integer user_id
 * @property integer hospital_id
 */
class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'user_id',
        'hospital_id',

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

        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user_id' => [
                //add your user_id desired column to be search within
            ],
            'hospital_id' => [
                //add your hospital_id desired column to be search within
            ],

        ];
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hospital(): belongsTo
    {
        return $this->belongsTo(Hospital::class);
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
