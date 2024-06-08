<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\AppointmentStatusEnum;
use App\Enums\MediaTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Traits\Translations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Clinic extends Model implements HasMedia
{
    use HasFactory;
    use Translations;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'appointment_cost',
        'working_start_year',
        'max_appointments',
        'appointment_day_range',
        'about_us',
        'experience',
        'user_id',
        'hospital_id',
        "status",
        "approximate_appointment_time",
    ];

    protected $casts = [
        'name'               => Translatable::class,
        'working_start_year' => "datetime",
    ];

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
            "status",
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user'              => [
                "first_name",
                "middle_name",
                "last_name",
                'full_name'
            ],
            "user.address.city" => [
                "name",
            ],
            'user.phoneNumbers' => [
                'phone'
            ]
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name'     => 'is_archived',
                'relation' => 'user.is_archived',
            ],
            [
                'name' => 'status',
            ],
            [
                'name'     => 'city_name',
                'relation' => 'user.address.city.name',
                'operator' => 'like'
            ],
            [
                'name'     => 'day_of_week',
                'relation' => 'schedules.day_of_week',
                'method'   => 'whereTime'
            ],
            [
                'name'     => 'start_time',
                'relation' => 'schedules.start_time',
                'method'   => 'whereTime',
                'operator' => '>=',
            ],
            [
                'name'     => 'end_time',
                'relation' => 'schedules.end_time',
                'method'   => 'whereTime',
                'operator' => '<=',
            ]
        ];
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'work_gallery' => ['type' => MediaTypeEnum::MULTIPLE->value],
            //filesKeys
        ];
    }


    public function customOrders(): array
    {
        return [
            'user.address.city.name' => function (Builder $query, $dir) {
                return $query->join('users', 'users.id', '=', 'clinics.user_id')
                    ->join('addresses', function ($join) {
                        $join->on('addresses.addressable_id', '=', 'users.id')
                            ->where('addresses.addressable_type', User::class);
                    })
                    ->join('cities', 'cities.id', '=', 'addresses.city_id')
                    ->select('clinics.*', 'cities.name AS city_name')
                    ->orderBy('city_name', $dir);
            }
        ];
    }

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'clinic_specialities');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
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
        return $this->hasMany(Appointment::class)->where('date', '>', now()->addDay()->format('Y-m-d'));
    }

    public function canHasAppointmentIn(string $date, int $customerId): bool
    {
        if (!$this->validAppointmentDateTime($date)) {
            return false;
        }

        if ($this->hasHolidayIn($date)) {
            return false;
        }

        if (!$this->availableScheduleIn($date)) {
            return false;
        }

        $this->loadCount('validAppointments');

        // checking if the current clinic reached the maximum appointments per day
        return !($this->valid_appointments_count >= $this->max_appointments);
    }

    public function validAppointmentDateTime(string $date): bool
    {
        $date = Carbon::parse($date);

        return !($date->subDays($this->appointment_day_range)->isAfter(now()));
    }

    public function hasHolidayIn(string $date): bool
    {
        return $this->clinicHolidays()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    public function clinicHolidays(): HasMany
    {
        return $this->hasMany(ClinicHoliday::class);
    }

    public function availableScheduleIn(string $date): bool
    {
        $date = Carbon::parse($date);
        $dayName = Str::lower($date->dayName);

        return $this->schedules()
            ->where('day_of_week', $dayName)
            ->exists();
    }

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    public function validAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', [AppointmentStatusEnum::CHECKIN->value, AppointmentStatusEnum::BOOKED->value]);
    }

    public function validHolidays(): HasMany
    {
        return $this->hasMany(ClinicHoliday::class)
            ->where('end_date', '>=', now()->format('Y-m-d'));
    }

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class);
    }

    public function clinicSubscriptions(): HasMany
    {
        return $this->hasMany(ClinicSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ClinicSubscription::class)
            ->where('end_time', '>', now()->format('Y-m-d H:i:s'))
            ->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->latestOfMany();
    }

    public function isDeductable(): bool
    {
        $activeSubscription = $this->activeSubscription;
        return $activeSubscription?->subscription?->period == -1
            && $activeSubscription?->deduction_cost != 0
            && $activeSubscription?->subscription?->cost == 0;
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function patientProfiles()
    {
        return $this->hasMany(PatientProfile::class);
    }
}
