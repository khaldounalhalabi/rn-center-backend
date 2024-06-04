<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Traits\Translations;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer  clinic_id
 * @property DateTime start_date
 * @property DateTime end_date
 * @property string   reason
 * @property Clinic   clinic
 */
class ClinicHoliday extends Model
{
    use HasFactory;
    use Translations;


    protected $fillable = [
        'clinic_id',
        'start_date',
        'end_date',
        'reason',
    ];

    protected $casts = [
        'reason'     => Translatable::class,
        'start_date' => 'datetime:Y-m-d',
        'end_date'   => 'datetime:Y-m-d',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'reason',
            'start_date',
            'end_date',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinic' => [
                'name',
            ]
        ];
    }

    public function clinic(): BelongsTo
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

    public function filterArray(): array
    {
        return [
            [
                'name'     => 'start_date',
                'method'   => 'whereDate',
                'operator' => '>=',

            ],
            [
                'name'     => 'end_date',
                'method'   => 'whereDate',
                'operator' => '<=',
            ],
        ];
    }
}
