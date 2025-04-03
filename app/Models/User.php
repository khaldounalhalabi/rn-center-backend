<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Enums\MediaTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Serializers\Translatable as TranslatableSerializer;
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
 * @property TranslatableSerializer first_name
 * @property TranslatableSerializer last_name
 * @property TranslatableSerializer fullName
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
        'first_name',
        'last_name',
        'email',
        'birth_date',
        'gender',
        'blood_group',
        'image',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'datetime',
        'birth_date' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'first_name' => Translatable::class,
        'last_name' => Translatable::class,
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'first_name', 'last_name',
            'email', 'birth_date',
            'gender', 'blood_group',
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

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RolesPermissionEnum::ADMIN['role']);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole(RolesPermissionEnum::CUSTOMER['role']);
    }

    public function isClinic(): bool
    {
        return $this->isDoctor() || $this->isClinicEmployee();
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(RolesPermissionEnum::DOCTOR['role']) && $this?->clinic?->exists();
    }

    public function isClinicEmployee(): bool
    {
        return $this->hasRole(RolesPermissionEnum::CLINIC_EMPLOYEE['role']) && $this?->clinicEmployee?->clinic?->exists();
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

    public function phoneNumbers(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => Hash::make($value)
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => new TranslatableSerializer([
                'en' => $this->first_name->en . ' ' . $this->last_name->en,
                'ar' => $this->first_name->ar . ' ' . $this->last_name->ar,
            ]),
        );
    }
}
