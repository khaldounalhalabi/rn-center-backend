<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Serializers\Translatable as TranslatableSerializer;
use App\Traits\HasRoles;
use Carbon\Carbon;
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
 * @property TranslatableSerializer full_name
 * @property TranslatableSerializer first_name
 * @property TranslatableSerializer middle_name
 * @property TranslatableSerializer last_name
 * @property Carbon                 reset_code_valid_until
 * @mixin Builder
 */
class User extends Authenticatable implements HasMedia, JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;

    protected $guarded = ['id'];

    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'email', 'birth_date',
        'gender', 'blood_group', 'is_blocked',
        'tags', 'image', 'email_verified_at',
        'password', 'fcm_token', 'reset_password_code',
        'is_archived', 'remember_token', 'verification_code',
        'full_name', 'reset_code_valid_until',
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
        'reset_code_valid_until' => 'datetime:Y-m-d H:i:s',
        'first_name' => Translatable::class,
        'middle_name' => Translatable::class,
        'last_name' => Translatable::class,
        'full_name' => Translatable::class,
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
            'tags', 'is_archived', 'full_name',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'roles' => [
                'name',
            ],
            'phoneNumbers' => [
                'phone',
            ],
            'address.city' => [
                'name',
            ],
        ];
    }

    protected static function booted(): void
    {
        parent::booted();
        self::creating(function (User $user) {
            $user->full_name = self::getUserFullName($user->first_name->toJson(), $user->middle_name->toJson(), $user->last_name->toJson());
        });
    }

    public static function getUserFullName($firstName, $middleName, $lastName): string|false
    {
        if ($firstName instanceof TranslatableSerializer && $middleName instanceof TranslatableSerializer && $lastName instanceof TranslatableSerializer) {
            return json_encode([
                'en' => $firstName->en . ' ' . $middleName->en . ' ' . $lastName->en,
                'ar' => $firstName->ar . ' ', $middleName->ar . ' ' . $lastName->ar,
            ], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        }

        if (is_array($firstName) &&
            is_array($middleName) &&
            is_array($lastName)) {
            return json_encode([
                'en' => ($firstName['en'] ?? '') . ' ' . ($middleName['en'] ?? '') . ' ' . ($lastName['en'] ?? ''),
                'ar' => ($firstName['ar'] ?? '') . ' ', ($middleName['ar'] ?? '') . ' ' . ($lastName['ar'] ?? ''),
            ], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        }

        return json_encode([
            'en' => (json_decode($firstName, true)['en'] ?? '') . ' ' . (json_decode($middleName, true)['en'] ?? '') . ' ' . (json_decode($lastName, true)['en'] ?? ''),
            'ar' => (json_decode($firstName, true)['ar'] ?? '') . ' ' . (json_decode($middleName, true)['ar'] ?? '') . ' ' . (json_decode($lastName, true)['ar'] ?? ''),
        ], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }

    public function customOrders(): array
    {
        return [
            'address.city.name' => function (Builder $query, $dir) {
                return $query->join('addresses', function ($join) {
                    $join->on('addresses.addressable_id', '=', 'users.id')
                        ->where('addresses.addressable_type', User::class);
                })
                    ->join('cities', 'cities.id', '=', 'addresses.city_id')
                    ->select('users.*', 'cities.name AS city_name')
                    ->orderBy('city_name', $dir);
            },
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

    public function isBlocked(): bool
    {
        if ($this->is_blocked) {
            return true;
        }

        $this->load('phones');

        return BlockedItem::whereIn('value', [
            $this->email,
            ...$this->phones->pluck('phone'),
            $this->full_name->en,
            $this->full_name->ar,
        ])->exists();
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => Hash::make($value)
        );
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(RolesPermissionEnum::DOCTOR['role']) && $this?->clinic?->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RolesPermissionEnum::ADMIN['role']);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole(RolesPermissionEnum::CUSTOMER['role']);
    }

    public function isClinicEmployee(): bool
    {
        return $this->hasRole(RolesPermissionEnum::CLINIC_EMPLOYEE['role']) && $this?->clinicEmployee?->clinic?->exists();
    }

    public function isClinic(): bool
    {
        return $this->isDoctor() || $this->isClinicEmployee();
    }

    public function getClinicId(): ?int
    {
        return $this->isDoctor()
            ? Clinic::withoutGlobalScopes()->where('user_id', $this->id)->first()?->id
            : ClinicEmployee::where('user_id', $this->id)->first()?->clinic_id;
    }

    public function clinicEmployee(): HasOne
    {
        return $this->hasOne(ClinicEmployee::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_blocked', false)
            ->where('is_archived', false);
    }

    public function isAvailable(): bool
    {
        return !$this->is_blocked && !$this->is_archived;
    }

    /**
     * to get admin current balance
     * where user type is for admin and clinic type is for clinics users
     * @return Balance|null
     */
    public function balance(): ?Balance
    {
        return Balance::where('balanceable_type', User::class)
            ->latest()->first();
    }


    public function platform(): HasOne
    {
        return $this->hasOne(UserPlatform::class, 'user_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread()
            ->where('type', 'NOT LIKE', '%RealTime%');
    }

    public function getClinic(array $relations = [], array $countable = [])
    {
        if (auth()?->user()?->isDoctor()) {
            return $this->clinic->load($relations)->loadCount($countable);
        } elseif (auth()?->user()?->isClinicEmployee()) {
            return $this->clinicEmployee->clinic->load($relations)->loadCount($countable);
        } else {
            return null;
        }
    }

    public function hasVerifiedPhoneNumber(): bool
    {
        return $this->phoneNumbers()->where('is_verified', true)->exists();
    }
}
