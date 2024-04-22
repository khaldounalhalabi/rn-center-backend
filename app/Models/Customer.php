<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property PatientProfile patient_profiles
 * @property Appointment appointments
 * @property string mother_full_name
 * @property string medical_condition
 * @property integer user_id
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_condition',
        'user_id',
    ];

    protected $casts = [

    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'medical_condition',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'patient_profiles' => [
                //add your patient_profiles desired column to be search within
            ],
            'appointments' => [
                //add your appointments desired column to be search within
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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
