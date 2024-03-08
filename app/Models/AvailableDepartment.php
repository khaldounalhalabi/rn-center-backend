<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string name
 * @property string description
 * @property integer hospital_id
 */
class AvailableDepartment extends Model
{
    use HasFactory;
    use \App\Traits\Translations;


    protected $fillable = [
        'name',
        'description',
        'hospital_id',

    ];

    protected $casts = [
        'name' => \App\Casts\Translatable::class,
        'description' => \App\Casts\Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'description',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'hospital_id' => [
                //add your hospital_id desired column to be search within
            ],

        ];
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
