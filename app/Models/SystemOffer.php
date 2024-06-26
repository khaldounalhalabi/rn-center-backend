<?php

namespace App\Models;

use App\Enums\MediaTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string  title
 * @property ?string description
 * @property string  type
 * @property numeric amount
 * @property numeric allowed_uses
 */
class SystemOffer extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'type',
        'amount',
        'allowed_uses',
        'allow_reuse',
        'from',
        'to'
    ];

    protected $casts = [
        'allow_reuse'  => 'boolean',
        'allowed_uses' => 'integer',
        'amount'       => 'double',
        'from'         => 'date',
        'to'           => 'date',
    ];

    public function exportable(): array
    {
        return [
            'title',
            'description',
            'type',
            'amount',
            'allowed_uses',
            'allow_reuse',
        ];
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_system_offers');
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_system_offers');
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'image' => ['type' => MediaTypeEnum::SINGLE->value]
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
            'description',
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

    public function scopeAllowReuse($query)
    {
        return $query->where('allow_reuse', 1);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_system_offers');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('to', '>=', now()->format('Y-m-d'))
            ->where(function ($query) {
                $query->where(DB::raw('(
                    select count(*) from customers
                    inner join customer_system_offers
                    on customers.id = customer_system_offers.customer_id
                    where system_offers.id = customer_system_offers.system_offer_id
                )')
                    , '<', DB::raw('system_offers.allowed_uses'));
            });
    }

    public function isActive(): bool
    {
        return $this->to->greaterThanOrEqualTo(now()->format('Y-m-d'))
            && $this->customers()->count() < $this->allowed_uses;
    }
}
