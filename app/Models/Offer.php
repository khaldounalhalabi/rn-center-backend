<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasAbilities;
use App\Traits\Translations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
    use HasAbilities;
    use InteractsWithMedia;

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
        'title' => Translatable::class,
        'note' => Translatable::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function authorizedActions(): array
    {
        return [
            'manage-offers',
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'title',
            'type',
            'value',
            'start_at',
            'end_at',
            'is_active',
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
            if ($offer->end_at->isBefore(now()) || $offer->start_at->isAfter(now())) {
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

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', 1)
            ->where('start_at', '<=', now()->format('Y-m-d'))
            ->where('end_at', '>=', now()->format('Y-m-d'));
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_offers');
    }

    public function canShow(): bool
    {
        return ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic?->isAvailable())
            || auth()->user()?->isAdmin()
            || (
                (!auth()->user() || auth()->user()?->isCustomer())
                && $this->clinic?->isAvailable()
                && $this->clinic?->availableOnline()
                && $this->is_active
            );
    }
}
