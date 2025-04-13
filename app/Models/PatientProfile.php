<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Enums\MediaTypeEnum;
use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property integer  customer_id
 * @property integer  clinic_id
 * @property string   medical_condition
 * @property string   note
 * @property string   other_data
 * @property Customer customer
 * @property Clinic   clinic
 */
class PatientProfile extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasAbilities;

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'medical_condition',
        'note',
        'other_data',
    ];

    protected $casts = [

    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'medical_condition',
            'note',
            'other_data',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinic' => [
                'name',
            ],
            'clinic.user' => [

            ],
            'customer.user' => [
            ],
        ];
    }

    public function exportable(): array
    {
        return [
            'medical_condition',
            'note',
            'other_data',
            'customer.mother_full_name',
            'clinic.name',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
            'images' => ['type' => MediaTypeEnum::MULTIPLE->value],
        ];
    }

    public function canShow(): bool
    {
        return
            ($this->clinic_id == clinic()?->id)
            || isAdmin()
            || (isCustomer() && $this->customer_id == auth()->user()?->customer->id);
    }

    public function lastAppointment(): HasOne
    {
        return $this->hasOne(Appointment::class, 'customer_id', 'customer_id')
            ->ofMany([
                'date' => 'max'
            ], function ($query) {
                $query->where('status', AppointmentStatusEnum::CHECKOUT->value)
                    ->where('clinic_id', clinic()?->id);
            });
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id', 'customer_id')
            ->where('clinic_id', clinic()?->id);
    }
}
