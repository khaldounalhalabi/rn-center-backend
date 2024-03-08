<?php

namespace App\Models;

use App\Enums\MediaTypeEnum;
use App\Traits\Translations;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property Department departments
 * @property Offer offers
 * @property Subscription subscriptions
 * @property Transaction transactions
 * @property Appointment appointments
 * @property PatientProfile patient_profiles
 * @property Specialty specialties
 * @property Prescription prescriptions
 * @property Service services
 * @property string name
 * @property float appointment_cost
 * @property integer user_id
 * @property DateTime working_start_year
 * @property integer max_appointments
 * @property integer appointment_day_range
 * @property string about_us
 * @property string experience
 */
class Clinic extends Model implements HasMedia
{
    use HasFactory;
    use Translations;
    use InteractsWithMedia;


    protected $fillable = [
        'name',
        'appointment_cost',
        'user_id',
        'working_start_year',
        'max_appointments',
        'appointment_day_range',
        'about_us',
        'experience',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'about_us',
            'experience',

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
            'user_id' => [
                //add your user_id desired column to be search within
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

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class);
    }
}
