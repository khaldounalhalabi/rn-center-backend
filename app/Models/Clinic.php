<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Interfaces\ActionsMustBeAuthorized;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @method Builder|Clinic online
 */
class Clinic extends Model implements ActionsMustBeAuthorized
{
    use HasFactory;

    protected $fillable = [
        'appointment_cost',
        'working_start_year',
        'max_appointments',
        'user_id',
    ];

    protected $casts = [
        'max_appointments' => 'integer',
        'appointment_cost' => 'float',
    ];

    public static function authorizedActions(): array
    {
        return [
            'edit-clinic-profile',
            'show-clinic-profile',
        ];
    }

    /**
     * add your searchable columns,so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'appointment_cost',
            'working_start_year',
            'max_appointments',
            'appointment_day_range',
            'status',
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
                'first_name',
                'last_name',
                'phone',
                'gender'
            ],
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'day_of_week',
                'relation' => 'schedules.day_of_week',
            ],
            [
                'name' => 'available_time',
                'query' => fn(Builder|Clinic $query, $value) => $query
                    ->whereHas('schedules', function (Builder|Schedule $schedule) use ($value) {
                        $schedule->whereTime('start_time', ">=", $value)
                            ->whereTime('end_time', "<=", $value);
                    })
            ],
            [
                'name' => 'end_time',
                'relation' => 'schedules.end_time',
                'method' => 'whereTime',
                'operator' => '<=',
            ],
        ];
    }

    public function filesKeys(): array
    {
        return [
        ];
    }

    public function customOrders(): array
    {
        return [

        ];
    }

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'clinic_specialities');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function todayAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('date', now()->format('Y-m-d'));
    }

    public function upcomingAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('date', '>', now()->addDay()->format('Y-m-d'))
            ->whereNotIn('status', [AppointmentStatusEnum::CANCELLED->value, AppointmentStatusEnum::PENDING->value]);
    }

    public function canHasAppointmentIn(string $date): bool
    {
        if (!$this->validAppointmentDateTime($date)) {
            return false;
        }

        if (!$this->availableScheduleIn($date)) {
            return false;
        }

        $this->loadCount('validAppointments');

        // checking if the current clinic reached the maximum appointments per day
        return !(
            $this->validAppointments()
                ->where('date', Carbon::parse($date)->format('Y-m-d'))
                ->count()
            >=
            $this->max_appointments
        );
    }

    public function validAppointmentDateTime(string $date): bool
    {
        $date = Carbon::parse($date);

        return !($date->subDays(($this->appointment_day_range ?? 0) > 0
            ? $this->appointment_day_range - 1
            : 0
        )->isAfter(now()));
    }

    public function availableScheduleIn(string $date): bool
    {
        Carbon::setLocale("en");
        $date = Carbon::parse($date);
        $dayName = Str::lower($date->dayName);

        return $this->schedules()
            ->where('day_of_week', $dayName)
            ->exists();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function validAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', [AppointmentStatusEnum::CHECKIN->value, AppointmentStatusEnum::BOOKED->value]);
    }

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function patientProfiles(): HasMany
    {
        return $this->hasMany(PatientProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
