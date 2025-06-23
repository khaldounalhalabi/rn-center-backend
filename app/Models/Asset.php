<?php

namespace App\Models;

use App\Enums\AssetStatusEnum;
use App\Enums\AssetTypeEnum;
use App\Enums\MediaTypeEnum;
use App\Enums\PermissionEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int         id
 * @property string      name
 * @property null|string serial_number
 * @property string      type
 * @property numeric     quantity
 * @property null|Carbon purchase_date
 * @property string|null quantity_unit
 */
class Asset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'serial_number',
        'type',
        'quantity',
        'purchase_date',
        'quantity_unit',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'name',
            'serial_number',
            'type',
            'quantity',
            'purchase_date',
            'quantity_unit',
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
            'image' => ['type' => MediaTypeEnum::SINGLE->value],
        ];
    }

    public function filterArray(): array
    {
        return [
            ['name' => 'type',],
            [
                'name' => 'user_id',
                'query' => fn(Asset|Builder $q, $val) => $q->whereHas(
                    'assignedUsers',
                    fn(UserAsset|Builder $q1) => $q1->where('user_id', $val)
                ),
            ],
            [
                'name' => 'availability_status',
                'query' => fn(Asset|Builder $q, $val) => $q->where(function (Builder $query) use ($val) {
                    $query->whereHas(
                        'userAssets',
                        fn(UserAsset|Builder $q1) => $q1->where('status', $val)
                    )->when(
                        $val == AssetStatusEnum::CHECKOUT->value,
                        fn(Asset|Builder $q2) => $q2->orWhereDoesntHave('userAssets')
                    );
                }),
            ],
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'serial_number',
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
            'userAssets' => [
                'status',
            ],
        ];
    }

    public function canCheckin(): bool
    {
        return (isAdmin() || can(PermissionEnum::ASSETS_MANAGEMENT))
            && $this->quantity > 0;
    }

    /**
     * don't delete even if the IDE showing it is not used
     * @return bool
     */
    public function canCheckout(): bool
    {
        return (
                isAdmin()
                || can(PermissionEnum::ASSETS_MANAGEMENT)
            ) && in_array($this->type, AssetTypeEnum::needCheckout())
            && ($this->assigned_users_count ?? $this->assignedUsers()->count()) > 0;
    }

    public function userAssets(): HasMany
    {
        return $this->hasMany(UserAsset::class);
    }

    public function assignedUserAssets(): HasMany
    {
        return $this->hasMany(UserAsset::class)->where('status', AssetStatusEnum::CHECKIN->value);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_assets')
            ->wherePivot('status', AssetStatusEnum::CHECKIN->value);
    }

    public function isAsset(): bool
    {
        return $this->type == AssetTypeEnum::ASSET->value;
    }

    public function needCheckout(): bool
    {
        return in_array($this->type, AssetTypeEnum::needCheckout());
    }
}
