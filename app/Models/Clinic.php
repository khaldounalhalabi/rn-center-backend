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
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
    ];

    protected $casts = [
        'name' => Translatable::class,
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
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'departments' => [
                //add your departments desired column to be search within
            ],
            'offers' => [
                //add your offers desired column to be search within
            ],
            'subscriptions' => [
                //add your subscriptions desired column to be search within
            ],
            'transactions' => [
                //add your transactions desired column to be search within
            ],
            'appointments' => [
                //add your appointments desired column to be search within
            ],
            'patient_profiles' => [
                //add your patient_profiles desired column to be search within
            ],
            'specialties' => [
                //add your specialties desired column to be search within
            ],
            'prescriptions' => [
                //add your prescriptions desired column to be search within
            ],
            'services' => [
                //add your services desired column to be search within
            ],
            'user' => [
                "first_name",
                "middle_name",
                "last_name",
            ],
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
            'address.city' => function (Builder $query) {
                return $query->orderBy(function (QueryBuilder $q) {
                    return $q->from('users')
                        ->whereRaw('`users`.id = clinics.user_id')
                        ->orderBy(function (QueryBuilder $builder) {
                            return $builder->from('addresses')
                                ->whereRaw('`addresses`.addressable_id = `users`.id && `addresses`.addressable_type = ' . User::class)
                                ->select('city');
                        });
                });
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
}
