<?php

namespace App\Models;

use App\Enums\MediaTypeEnum;
use App\Enums\PermissionEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int    user_id
 * @property Carbon birth_date
 * @property string blood_group
 */
class Customer extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'birth_date',
        'blood_group',
        'health_status',
        'notes',
        'other_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'birth_date' => 'datetime',
        'other_data' => 'array',
    ];

    public function searchableArray(): array
    {
        return [
            'birth_date',
            'blood_group',
            'health_status',
            'notes',
            'other_data',
        ];
    }

    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'email',
                'first_name',
                'last_name',
                'phone',
                'full_name',
            ],
        ];
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function filesKeys(): array
    {
        return [
            'attachments' => ['type' => MediaTypeEnum::MULTIPLE->value],
        ];
    }

    public function scopeByClinic(Builder $query, int $clinicId): Builder
    {
        return $query->whereHas('appointments', function (Appointment|Builder $appointment) use ($clinicId) {
            $appointment->where('clinic_id', $clinicId);
        })->orWhereHas('medicalRecords', function (Builder|MedicalRecord $medicalRecord) use ($clinicId) {
            $medicalRecord->where('clinic_id', $clinicId);
        });
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function patientStudies(): HasMany
    {
        return $this->hasMany(PatientStudy::class);
    }

    public function canManage(): bool
    {
        return isAdmin()
            || (isSecretary() && can(PermissionEnum::PATIENT_MANAGEMENT))
            || (isDoctor() && (
                    $this->appointments()->where('clinic_id', clinic()->id)->exists()
                    || $this->medicalRecords()->where('clinic_id', clinic()->id)->exists()
                    || $this->prescriptions()->where('clinic_id', clinic()->id)->exists()
                ))
            || (isCustomer() && $this->id == customer()->id);
    }
}
