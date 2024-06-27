<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasClinic;
use App\Traits\Translations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property numeric value
 * @property Carbon  start_at
 * @property Carbon  end_at
 * @property string  type
 * @property integer clinic_id
 * @property Clinic  clinic
 */
class Offer extends Model implements ActionsMustBeAuthorized, HasMedia
{
    use HasFactory;
    use Translations;
    use HasClinic;
    use InteractsWithMedia;

    public static function authorizedActions(): array
    {
        return [
            'manage-offers'
        ];
    }

    protected $fillable = [
        'title',
        'value',
        'note',
        'start_at',
        'end_at',
        'is_active',
        'type',
        'clinic_id',
    ];
    protected $casts = [
        'title'     => Translatable::class,
        'note'      => Translatable::class,
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'type',
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

    protected static function booted(): void
    {
        parent::booted();
        self::creating(function (Offer $offer) {
            if ($offer->end_at->isBefore(now())) {
                $offer->is_active = false;
            } else {
                $offer->is_active = true;
            }
        });
    }

    public function exportable(): array
    {
        return [
            'title',
            'value',
            'note',
            'start_at',
            'end_at',
            'is_active',
            'type',
            'clinic.name',
        ];
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
            'image' => ['type' => MediaTypeEnum::SINGLE->value]];
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_offers');
    }
}
