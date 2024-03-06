<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer clinic_id
 * @property string day_of_week
 * @property DateTime start_time
 * @property DateTime end_time
 * @property integer hospital_id
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'day_of_week',
        'start_time',
        'end_time',
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
            'day_of_week',

        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinic_id' => [
                //add your clinic_id desired column to be search within
            ],
            'hospital_id' => [
                //add your hospital_id desired column to be search within
            ],

        ];
    }

    public function clinic(): belongsTo
    {
        return $this->belongsTo(Clinic::class);
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
