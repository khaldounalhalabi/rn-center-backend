<?php

namespace App\Models;

use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasClinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property PatientProfile patient_profiles
 * @property Appointment    appointments
 * @property string         mother_full_name
 * @property string         medical_condition
 * @property integer        user_id
 */
class Customer extends Model implements ActionsMustBeAuthorized
{
    use HasFactory;
    use HasClinic;

    public static function authorizedActions(): array
    {
        return [
            'manage-patients'
        ];
    }

    protected $fillable = [
        'user_id',
    ];

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'email',
                'full_name'
            ],
        ];
    }

    public function customOrders(): array
    {
        return [
            'user.first_name' => function (Builder $query, $dir) {
                return $query->join('users', 'users.id', '=', 'customers.user_id')
                    ->select('customers.*', 'users.full_name AS customer_first_name')
                    ->orderBy('customer_first_name', $dir);
            }
        ];
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function patientProfiles(): HasMany
    {
        return $this->hasMany(PatientProfile::class);
    }

    public function currentClinicPatientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class)
            ->where('clinic_id', auth()->user()?->getClinicId())
            ->latestOfMany();
    }

    public function canShow(): bool
    {
        return (
                auth()->user()?->isClinic()
                && $this->patientProfiles()
                    ->where('clinic_id', auth()?->user()?->getClinicId())->exists()
            ) || auth()->user()?->isAdmin();
    }

    public function canUpdate(): bool
    {
        return (
                auth()->user()?->isClinic()
                && $this->patientProfiles()
                    ->where('clinic_id', auth()?->user()?->getClinicId())->exists()
            ) || auth()->user()?->isAdmin();
    }

    public function canDelete(): bool
    {
        return auth()->user()?->isAdmin();
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('user', function (Builder $q) {
            $q->available();
        });
    }

    public function systemOffers(): BelongsToMany
    {
        return $this->belongsToMany(SystemOffer::class, 'customer_system_offers');
    }
}
