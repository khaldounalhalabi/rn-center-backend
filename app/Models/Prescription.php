<?php

namespace App\Models;

use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;
    use HasAbilities;

    protected $fillable = [
        'clinic_id',
        'customer_id',
        'appointment_id',
        'other_data',
        'next_visit',
    ];

    protected $casts = [
        'other_data' => 'array',
        'next_visit' => 'datetime',
    ];

    public static function searchableArray(): array
    {
        return [
            'other_data',
            'next_visit',
        ];
    }

    public static function relationsSearchableArray(): array
    {
        return [
            'clinic.user' => [
                'full_name'
            ],
            'customer.user' => [
                'full_name',
            ],
            'appointment' => [
                'date_time'
            ]
        ];
    }

    public function exportable(): array
    {
        return [
            'clinic.user.full_name',
            'customer.user.full_name',
            'appointment.date_time',
            'next_visit',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'medicine_prescriptions');
    }

    public function medicinePrescriptions(): HasMany
    {
        return $this->hasMany(MedicinePrescription::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
