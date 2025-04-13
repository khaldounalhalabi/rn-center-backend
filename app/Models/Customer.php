<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasAbilities;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int    user_id
 * @property Carbon birth_date
 * @property string blood_group
 */
class Customer extends Model implements ActionsMustBeAuthorized
{
    use HasAbilities;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'blood_group',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'birth_date' => 'datetime',
    ];

    public static function authorizedActions(): array
    {
        return [
            'manage-patients',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'email',
                'first_name',
                'last_name',
                'phone'
            ],
        ];
    }

    public function customOrders(): array
    {
        return [

        ];
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function currentClinicPatientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class)
            ->where('clinic_id', clinic()?->id)
            ->latestOfMany();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canShow(): bool
    {
        return (
                isDoctor()
                && (
                    $this->patientProfiles()->where('clinic_id', clinic()?->id)->exists()
                    || $this->appointments()->where('clinic_id', clinic()?->id)->exists()
                )
            ) || isAdmin();
    }

    public function patientProfiles(): HasMany
    {
        return $this->hasMany(PatientProfile::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function canUpdate(): bool
    {
        return (
                isDoctor()
                && (
                    $this->patientProfiles()->where('clinic_id', clinic()?->id)->exists()
                    || $this->appointments()->where('clinic_id', clinic()?->id)->exists()
                )
            ) || isAdmin();
    }

    public function canDelete(): bool
    {
        return isAdmin();
    }

    public function validAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)
            ->whereNotIn('status', [AppointmentStatusEnum::CANCELLED->value, AppointmentStatusEnum::PENDING->value]);
    }
}
