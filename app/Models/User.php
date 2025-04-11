<?php

namespace App\Models;

use App\Enums\RolesPermissionEnum;
use App\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @property integer     id
 * @property string      first_name
 * @property string      last_name
 * @property string|null email
 * @property string      password
 * @property string      phone
 * @property string      remember_token
 * @property Carbon      created_at
 * @property Carbon      updated_at
 * @property Carbon|null phone_verified_at
 * @property string      gender
 * @property string      full_name
 * @mixin Builder
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $guarded = ['id'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'phone_verified_at' => 'datetime'
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
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

    public function customOrders(): array
    {
        return [

        ];
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [];
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
        return $this->isDoctor();
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(RolesPermissionEnum::DOCTOR['role']) && $this?->clinic?->exists();
    }

    public function getClinicId(): ?int
    {
        return Clinic::withoutGlobalScopes()->where('user_id', $this->id)->first()?->id;
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
        } else {
            return null;
        }
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
            get: fn($value, array $attributes) => $this->first_name . ' ' . $this->last_name,
        );
    }


    public function verified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function verify(): static
    {
        $this->update([
            'phone_verified_at' => now()
        ]);
        return $this;
    }

    public function unVerify(): static
    {
        $this->update([
            'phone_verified_at' => null
        ]);

        return $this;
    }

    protected function universalPhone(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => preg_replace('/^0/', '+963', $this->phone),
        );
    }

    public function isSecretary(): bool
    {
        return $this->hasRole(RolesPermissionEnum::SECRETARY['role']);
    }
}
