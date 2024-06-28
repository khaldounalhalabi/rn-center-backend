<?php

namespace App\Models;

use App\Traits\HasClinic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property numeric          amount
 * @property null|integer     appointment_id
 * @property string           type
 * @property int              clinic_id
 * @property null|string      notes
 * @property string           status
 * @property Appointment|null appointment
 * @property Clinic           clinic
 * @property Carbon           date
 */
class ClinicTransaction extends Model
{
    use HasFactory;
    use HasClinic;

    protected $fillable = [
        'amount',
        'appointment_id',
        'type',
        'clinic_id',
        'notes',
        'status',
        'date',
    ];

    protected $casts = [
        'amount' => 'double',
        'date'   => 'date'
    ];

    public function exportable(): array
    {
        return [
            'amount',
            'type',
            'notes',
            'status',
            'appointment.id',
            'appointment.date',
            'appointment.customer.user.full_name',
            'date',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
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

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'type',
            'notes',
            'status',
            'date',
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'type',
            ],
            [
                'name' => 'date',
            ],
            [
                'name' => 'status',
            ],
            [
                'name' => 'amount',
            ],
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'appointment.customer.user' => [
                'full_name'
            ]
        ];
    }
}
