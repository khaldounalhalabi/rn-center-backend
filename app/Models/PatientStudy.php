<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string   uuid
 * @property string   patient_uuid
 * @property int      customer_id
 * @property string   study_uuid
 * @property string   study_uid
 * @property Customer customer
 * @property string   title
 * @property Carbon   study_date
 * @property array    available_modes
 */
class PatientStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'patient_uuid',
        'customer_id',
        'study_uuid',
        'study_uid',
        'title',
        'study_date',
        'available_modes'
    ];

    protected $casts = [
        'study_date' => 'datetime',
        'available_modes' => 'array'
    ];

    public function exportable(): array
    {
        return [
            'uuid',
            'patient_uuid',
            'study_uuid',
            'study_uid',
            'customer.mother_full_name',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'uuid',
            'patient_uuid',
            'study_uuid',
            'study_uid',

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
}
