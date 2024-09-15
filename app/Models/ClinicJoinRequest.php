<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string doctor_name
 * @property string clinic_name
 * @property string phone_number
 * @property int city_id
 * @property City city
 */
class ClinicJoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_name',
        'clinic_name',
        'phone_number',
        'city_id',

    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'doctor_name',
            'clinic_name',
            'phone_number',
            'city.name',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            //filesKeys
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'doctor_name',
            'clinic_name',
            'phone_number',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [

        ];
    }
}
