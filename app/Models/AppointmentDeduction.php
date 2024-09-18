<?php

namespace App\Models;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Enums\AppointmentStatusEnum;
use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property numeric           amount
 * @property string            status
 * @property int               clinic_transaction_id
 * @property int               appointment_id
 * @property int               clinic_id
 * @property int|null          transaction_id
 * @property string            date
 * @property ClinicTransaction clinicTransaction
 * @property Appointment       appointment
 * @property Clinic            clinic
 */
class AppointmentDeduction extends Model
{
    use HasFactory;
    use HasAbilities;

    protected $fillable = [
        'amount',
        'status',
        'clinic_transaction_id',
        'appointment_id',
        'clinic_id',
        'date',
        'transaction_id'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'amount',
            'status',
            'date',
            'appointment.date',
        ];
    }

    public function clinicTransaction(): BelongsTo
    {
        return $this->belongsTo(ClinicTransaction::class);
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
            'status',
            'amount',
            'date',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'appointment.clinic.user' => [
                'full_name',
            ],
            'appointment.clinic'      => [
                'name',
            ],
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'date',
            ],
            [
                'name' => 'status'
            ],
            [
                'name' => 'clinic_id'
            ]
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isPending(): bool
    {
        return $this->status == AppointmentStatusEnum::PENDING->value;
    }

    public function isDone(): bool
    {
        return $this->status == AppointmentDeductionStatusEnum::DONE->value;
    }
}
