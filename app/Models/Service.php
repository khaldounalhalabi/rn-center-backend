<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Traits\HasClinic;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property numeric         approximate_duration
 * @property integer         service_category_id
 * @property numeric         price
 * @property numeric         status
 * @property integer         clinic_id
 * @property ServiceCategory serviceCategory
 * @property Clinic          clinic
 */
class Service extends Model implements HasMedia
{
    use HasFactory;
    use Translations;
    use InteractsWithMedia;
    use HasClinic;

    protected $fillable = [
        'name',
        'approximate_duration',
        'service_category_id',
        'price',
        'status',
        'description',
        'clinic_id',
    ];

    protected $casts = [
        'name'        => Translatable::class,
        'description' => Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'approximate_duration',
            'price',
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
            'serviceCategory' => [
                'name',
            ]
        ];
    }

    public function exportable(): array
    {
        return [
            'name',
            'approximate_duration',
            'price',
            'status',
            'description',
            'serviceCategory.name',
            'clinic.name',
        ];
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'icon' => ['type' => MediaTypeEnum::SINGLE->value]
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
