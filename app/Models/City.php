<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string name
 */
class City extends Model
{
    use HasFactory;
    use Translations;

    protected $fillable = [
        'name',

    ];

    protected $casts = [
        'name' => Translatable::class,

    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
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

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
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

    public function bloodDonationRequests(): HasMany
    {
        return $this->hasMany(BloodDonationRequest::class);
    }

    public function clinicJoinRequests(): HasMany
    {
        return $this->hasMany(ClinicJoinRequest::class);
    }
}
