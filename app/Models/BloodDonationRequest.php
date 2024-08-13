<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string      full_name
 * @property string      contact_phone
 * @property string      address
 * @property int         city_id
 * @property string      blood_group
 * @property numeric     nearest_hospital
 * @property null|string notes
 * @property City        city
 * @property string      can_wait_until
 */
class BloodDonationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact_phone',
        'address',
        'city_id',
        'blood_group',
        'nearest_hospital',
        'notes',
        'can_wait_until',
    ];

    protected $casts = [
        'can_wait_until' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'full_name',
            'contact_phone',
            'address',
            'blood_group',
            'nearest_hospital',
            'notes',
            'city.name',
        ];
    }

    public function city()
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
            'full_name',
            'contact_phone',
            'address',
            'blood_group',
            'notes',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'city' => [
                'name',
            ],
        ];
    }
}
