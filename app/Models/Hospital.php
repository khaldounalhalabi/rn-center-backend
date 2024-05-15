<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property Address addresses
 * @property string  name
 * @property string  phone_numbers
 * @property string  available_departments
 * @property string  images
 */
class Hospital extends Model implements HasMedia
{
    use HasFactory;
    use Translations;
    use InteractsWithMedia;

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
            'addresses' => [
                //add your addresses desired column to be search within
            ],
            'phoneNumbers' => [
                'phone'
            ]
        ];
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'images' => ["type" => MediaTypeEnum::MULTIPLE->value],
            //filesKeys
        ];
    }

    public function availableDepartments(): BelongsToMany
    {
        return $this->belongsToMany(AvailableDepartment::class, 'department_hospitals');
    }

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function phoneNumbers(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class);
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function customOrders(): array
    {
        return [
            'address.city.name' => function (Builder $query, $dir) {
                return $query->join('addresses', function ($join) {
                    $join->on('addresses.addressable_id', '=', 'hospitals.id')
                        ->where('addresses.addressable_type', Hospital::class);
                })->join('cities', 'cities.id', '=', 'addresses.city_id')
                    ->select('hospitals.*', 'cities.name AS city_name')
                    ->orderBy('city_name', $dir);
            }
        ];
    }
}
