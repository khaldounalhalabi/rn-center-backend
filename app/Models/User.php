<?php

namespace App\Models;

use App\Enums\RolesPermissionEnum;
use App\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @property int          id
 * @property string       first_name
 * @property string       last_name
 * @property string|null  email
 * @property string       password
 * @property string       phone
 * @property string       remember_token
 * @property Carbon       created_at
 * @property Carbon       updated_at
 * @property Carbon|null  phone_verified_at
 * @property string       gender
 * @property string       full_name
 * @property int|null     formula_id
 * @property Formula|null formula
 * @property string       fcm_token
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
        'formula_id',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'phone_verified_at' => 'datetime',
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
            'phone',
            'gender',
            'full_name',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (User $user) {
            $user->full_name = $user->first_name . ' ' . $user->last_name;
        });

        self::updating(function (User $user) {
            $user->full_name = $user->first_name . ' ' . $user->last_name;
        });
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'formula' => [
                'name',
                'formula',
                'slug',
                'template',
            ],
        ];
    }

    public function customOrders(): array
    {
        return [];
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

    public function isDoctor(): bool
    {
        return $this->hasRole(RolesPermissionEnum::DOCTOR['role']);
    }

    public function getClinicId(): ?int
    {
        return $this->clinic->id;
    }

    public function getClinic(array $relations = [], array $countable = [])
    {
        if (isDoctor()) {
            return $this->clinic->load($relations)->loadCount($countable);
        }

        return null;
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => Hash::make($value)
        );
    }

    public function verified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function verify(): static
    {
        $this->update([
            'phone_verified_at' => now(),
        ]);

        return $this;
    }

    public function unVerify(): static
    {
        $this->update([
            'phone_verified_at' => null,
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

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'scheduleable');
    }

    public function attendanceByDate(): HasMany
    {
        $date = Carbon::parse(request('attendance_at', now()));

        return $this->hasMany(AttendanceLog::class)
            ->where('attend_at', 'LIKE', "%{$date->format('Y-m-d')}%")
            ->orderBy('attend_at');
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function getSchedules(): MorphMany
    {
        if ($this->isDoctor()) {
            return $this->clinic->schedules();
        }

        return $this->schedules();
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
