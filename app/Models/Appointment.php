<?php

namespace App\Models;

use App\Enums\RolesPermissionEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer customer_id
 * @property integer clinic_id
 * @property string note
 * @property integer service_id
 * @property numeric extra_fees
 * @property numeric total_cost
 * @property string type
 * @property Carbon date
 * @property Carbon from
 * @property Carbon to
 * @property string status
 * @property string device_type
 * @property numeric appointment_sequence
 * @property string qr_code
 * @property Customer customer
 * @property Clinic clinic
 * @property Service service
 */
class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'note',
        'service_id',
        'extra_fees',
        'total_cost',
        'type',
        'date',
        'from',
        'to',
        'status',
        'device_type',
        'appointment_sequence',
        'qr_code',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d:',
        'from' => 'datetime:H:i:s',
        'to' => 'datetime:H:i:s',
    ];

    public function exportable(): array
    {
        return [
            'note',
            'extra_fees',
            'total_cost',
            'type',
            'date',
            'from',
            'to',
            'status',
            'device_type',
            'appointment_sequence',
            'qr_code',
            'customer.mother_full_name',
            'clinic.name',
            'service.name',
        ];
    }


    /**
     * return the full path of the stored QrCode
     * @return string|null
     */
    public function getQrCodePath(): ?string
    {
        return $this->QrCode != null ? asset('storage/' . $this->QrCode) : null;
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }


    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'qr_code',
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
            'id',
            'note',
            'type',
            'status',
            'device_type',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'customer.user' => [
                'first_name',
                'last_name',
                'middle_name'
            ],
            'clinic' => [
                'name',
            ],
            'clinic.user' => [
                'first_name',
                'last_name',
                'middle_name'
            ]
        ];
    }

    public function canUpdate(): bool
    {
        return auth()->user()->hasRole(RolesPermissionEnum::ADMIN['role'])
            || (auth()->user()->id == $this->clinic->user_id);
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'type',
            ],
            [
                'name' => 'status',
            ],
            [
                'name' => 'date',
            ],
            [
                'name' => 'from',
                'method' => 'whereTime',
                'operator' => '>=',
            ],
            [
                'name' => 'to',
                'method' => 'whereTime',
                'operator' => '<=',
            ],
        ];
    }

    public function appointmentLogs(): HasMany
    {
        return $this->hasMany(AppointmentLog::class);
    }
}
