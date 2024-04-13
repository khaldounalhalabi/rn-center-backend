<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
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
        'name' => Translatable::class,
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
            'user' => [
                "first_name",
                "middle_name",
                "last_name",
            ],
            "user.address.city" => [
                "name",
            ]
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'is_archived',
                'relation' => 'user.is_archived',
            ],
            [
                'name' => 'status',
            ],
            [
                'name' => 'city_name',
                'relation' => 'user.address.city.name',
                'operator' => 'like'
            ],
            [
                'name' => 'day_of_week',
                'relation' => 'schedules.day_of_week',
                'method' => 'whereTime'
            ],
            [
                'name' => 'start_time',
                'relation' => 'schedules.start_time',
                'method' => 'whereTime',
                'operator' => '>=' ,
            ],
            [
                'name' => 'end_time',
                'relation' => 'schedules.end_time',
                'method' => 'whereTime',
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

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'clinic_specialities');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function clinicHolidays(): HasMany
    {
        return $this->hasMany(ClinicHoliday::class);
    }
}
