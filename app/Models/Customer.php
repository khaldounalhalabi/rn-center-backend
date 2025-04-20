<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Traits\HasAbilities;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    user_id
 * @property Carbon birth_date
 * @property string blood_group
 */
class Customer extends Model
{
    use HasAbilities;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'blood_group',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'birth_date' => 'datetime',
    ];

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'email',
                'first_name',
                'last_name',
                'phone'
            ],
        ];
    }

    public function customOrders(): array
    {
        return [

        ];
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canShow(): bool
    {
        return isDoctor() || isAdmin();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function canUpdate(): bool
    {
        return isDoctor() || isAdmin();
    }

    public function canDelete(): bool
    {
        return isAdmin();
    }

    public function validAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)
            ->whereNotIn('status', [AppointmentStatusEnum::CANCELLED->value, AppointmentStatusEnum::PENDING->value]);
    }
}
