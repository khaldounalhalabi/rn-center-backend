<?php

namespace App\Models;

use App\Interfaces\ActionsMustBeAuthorized;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string   day_of_week
 * @property DateTime start_time
 * @property DateTime end_time
 * @property string   schedulable_type
 * @property int      schedulable_id
 */
class Schedule extends Model implements ActionsMustBeAuthorized
{
    use HasFactory;

    protected $fillable = [
        'schedulable_type',
        'schedulable_id',
        'day_of_week',
        'start_time',
        'end_time',
        'appointment_gap',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'day_of_week',
            'start_time',
            'end_time',
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

    public function filterArray(): array
    {
        return [
            [
                'name' => 'day_of_week'
            ],
            [
                'name'     => 'start_time',
                'operator' => '>='
            ],
            [
                'name'     => 'end_time',
                'operator' => '>='
            ]
        ];
    }

    public function schedulable(): belongsTo
    {
        return $this->morphTo();
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

    public static function authorizedActions(): array
    {
        return [
            'manage-schedules',
        ];
    }
}
