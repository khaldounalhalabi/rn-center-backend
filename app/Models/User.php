<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class User
 *
 * @mixin Builder
 */
class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use InteractsWithMedia;

    protected $guarded = ['id'];
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'email', 'birth_date',
        'gender', 'blood_group', 'is_blocked',
        'tags', 'image', 'email_verified_at',
        'password', 'fcm_token', 'reset_password_code',
        'is_archived', 'remember_token', 'verification_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'datetime',
        'birth_date' => 'datetime',
        'is_blocked' => 'boolean',
        'is_archived' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'first_name' => Translatable::class,
        'middle_name' => Translatable::class,
        'last_name' => Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'first_name', 'middle_name', 'last_name',
            'email', 'birth_date',
            'gender', 'blood_group', 'is_blocked',
            'tags', 'image', 'is_archived',
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

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('is_blocked', true);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->where('is_archived', false);
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
            //filesKeys
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function clinic(): HasOne
    {
        return $this->hasOne(Clinic::class);
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function phoneNumbers(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => Hash::make($value)
        );
    }
}
