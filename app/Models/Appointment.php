<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Enums\OfferTypeEnum;
use App\Interfaces\ActionsMustBeAuthorized;
use App\Notifications\Customer\AppointmentRemainingTimeNotification;
use App\Services\FirebaseServices;
use App\Traits\HasAbilities;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int      customer_id
 * @property int      clinic_id
 * @property string   note
 * @property int      service_id
 * @property numeric  extra_fees
 * @property numeric  total_cost
 * @property string   type
 * @property Carbon   date
 * @property string   status
 * @property string   device_type
 * @property numeric  appointment_sequence
 * @property string   qr_code
 * @property Customer customer
 * @property Clinic   clinic
 * @property Service  service
 * @property string   appointment_unique_code
 * @property boolean  is_revision
 */
class Appointment extends Model implements ActionsMustBeAuthorized
{
    use HasAbilities;
    use HasFactory;

    protected $observables = [
        'statusChange'
    ];
    protected $fillable = [
        'customer_id',
        'clinic_id',
        'note',
        'service_id',
        'extra_fees',
        'total_cost',
        'discount',
        'type',
        'date',
        'status',
        'device_type',
        'appointment_sequence',
        'qr_code',
        'remaining_time',
        'appointment_unique_code',
        'is_revision',
    ];
    protected $casts = [
        'date' => 'datetime:Y-m-d:',
        'is_revision' => 'boolean',
    ];

    public static function authorizedActions(): array
    {
        return [
            'manage-appointments',
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'id',
            'note',
            'type',
            'status',
            'device_type',
            'date',
            'appointment_unique_code',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    #[ArrayShape(['customer.user' => 'string[]', 'clinic' => 'string[]', 'clinic.user' => 'string[]'])]
    public static function relationsSearchableArray(): array
    {
        return [
            'customer.user' => [
                'first_name',
                'last_name',
            ],
            'clinic' => [
                'name',
            ],
            'clinic.user' => [
                'first_name',
                'last_name',
            ],
        ];
    }

    public static function handleRemainingTime(Appointment $appointment): Appointment
    {
        if ($appointment->status == AppointmentStatusEnum::BOOKED->value) {
            $appointment->load(['clinic', 'clinic.schedules']);
            $beforeAppointmentsCount = Appointment::validNotEnded()
                ->where('date', $appointment->date->format('Y-m-d'))
                ->where('clinic_id', $appointment->clinic_id)
                ->where('appointment_sequence', '<', $appointment->appointment_sequence)
                ->count();

            if ($beforeAppointmentsCount > 5) {
                return $appointment;
            }

            $appointment_gap = $appointment?->clinic?->schedules?->pluck('appointment_gap')->unique()->first();
            $approximate_appointment_time = $appointment?->clinic?->approximate_appointment_time;
            $diffDays = $appointment?->date?->diffInDays(now()->format('Y-m-d'));
            $diffMinutes = ($approximate_appointment_time + $appointment_gap) * $beforeAppointmentsCount;

            try {
                $diffTime = CarbonInterval::hours(intdiv($diffMinutes, 60))->minutes($diffMinutes % 60)->forHumans();
            } catch (Exception) {
                $diffTime = intdiv($diffMinutes, 60) . ' Hours  , ' . $diffMinutes % 60 . ' Minutes';
            }

            $appointment->remaining_time = $diffDays == 0
                ? "{$diffTime} , {$beforeAppointmentsCount} Patients Before You"
                : "{$diffDays} Days And {$diffTime} , {$beforeAppointmentsCount} Patients Before You";

            FirebaseServices::make()
                ->setData([
                    'remaining_time' => $appointment->remaining_time,
                    'message' => "Your appointment booked in ({$appointment?->clinic?->name}) clinic in {$appointment?->date?->format('Y-m-d')} has an approximate time of : {$appointment?->remaining_time}",
                    "message_ar" => $appointment?->remaining_time . "لديه من الوقت المتوفع " . $appointment?->clinic?->name?->ar . "عند عيادة" . $appointment?->date?->format('Y-m-d') . "موعدك المحجوز في تاريخ",
                    'appointment_id' => $appointment?->id,
                    'clinic_id' => $appointment?->clinic_id,
                    'url' => '#',
                ])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($appointment?->customer?->user)
                ->setNotification(AppointmentRemainingTimeNotification::class)
                ->send();
        }

        return $appointment;
    }

    protected static function booted(): void
    {
        parent::booted();
    }

    public function exportable(): array
    {
        return [
            'id',
            'service.name',
            'status',
            'type',
            'total_cost',
            'appointment_sequence',
            'date',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            'qr_code',
            //filesKeys
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'type',
            ],
            [
                'name' => 'status',
            ],
            [
                'name' => 'date',
            ],
            [
                'name' => 'service_id',
                'operator' => '=',
            ],
        ];
    }

    public function appointmentLogs(): HasMany
    {
        return $this->hasMany(AppointmentLog::class);
    }

    public function lastCheckinLog(): HasOne
    {
        return $this->hasOne(AppointmentLog::class)
            ->ofMany([
                'happen_in' => 'max',
            ], function ($query) {
                $query->where('status', AppointmentStatusEnum::CHECKIN->value);
            });
    }

    public function lastBookedLog(): HasOne
    {
        return $this->hasOne(AppointmentLog::class)
            ->ofMany([
                'happen_in' => 'max',
            ], function ($query) {
                $query->where('status', AppointmentStatusEnum::BOOKED->value);
            });
    }

    public function lastCheckoutLog(): HasOne
    {
        return $this->hasOne(AppointmentLog::class)
            ->ofMany([
                'happen_in' => 'max',
            ], function ($query) {
                $query->where('status', AppointmentStatusEnum::CHECKOUT->value);
            });
    }

    public function lastCancelledLog(): HasOne
    {
        return $this->hasOne(AppointmentLog::class)
            ->ofMany([
                'happen_in' => 'max',
            ], function ($query) {
                $query->where('status', AppointmentStatusEnum::CANCELLED->value);
            });
    }

    public function beforeAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'clinic_id', 'clinic_id')
            ->where('date', $this->date->format('Y-m-d'))
            ->where('appointment_sequence', '<', $this->appointment_sequence)
            ->where('id', '!=', $this->id)
            ->whereIn('status', [AppointmentStatusEnum::BOOKED->value, AppointmentStatusEnum::CHECKIN->value]);
    }

    #[ArrayShape(['clinic.user.first_name' => "\Closure", 'customer.user.first_name' => "\Closure"])]
    public function customOrders(): array
    {
        return [
            'clinic.user.first_name' => function (Builder $query, $dir) {
                return $query->join('clinics', 'clinics.id', '=', 'appointments.clinic_id')
                    ->join('users', function ($join) {
                        $join->on('users.id', '=', 'clinics.user_id');
                    })
                    ->select('appointments.*', 'users.first_name AS doctor_first_name')
                    ->orderBy('doctor_first_name', $dir);
            },
            'customer.user.first_name' => function (Builder $query, $dir) {
                return $query->join('customers', 'customers.id', '=', 'appointments.customer_id')
                    ->join('users', function ($join) {
                        $join->on('users.id', '=', 'customers.user_id');
                    })
                    ->select('appointments.*', 'users.first_name AS customer_first_name')
                    ->orderBy('customer_first_name', $dir);
            },
        ];
    }

    public function scopeValidNotEnded(Builder $query): Builder
    {
        return $query->whereIn('status', [AppointmentStatusEnum::BOOKED->value, AppointmentStatusEnum::CHECKIN->value]);
    }

    public function systemOffers(): BelongsToMany
    {
        return $this->belongsToMany(SystemOffer::class, 'appointment_system_offers');
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'appointment_offers');
    }

    public function clinicTransaction(): HasOne
    {
        return $this->hasOne(ClinicTransaction::class, 'appointment_id', 'id');
    }

    public function getClinicOfferTotal()
    {
        return $this->offers
            ->sum(fn(Offer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                ? $offer->amount
                : ($offer->amount * ($this->clinic->appointment_cost - $this->getSystemOffersTotal())) / 100
            );
    }

    public function getSystemOffersTotal()
    {
        return $this->systemOffers
            ->sum(fn(SystemOffer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                ? $offer->value
                : ($offer->value * $this->clinic->appointment_cost) / 100
            );
    }

    public function canShow(): bool
    {
        return
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || ($this->customer_id == auth()?->user()?->customer?->id && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin();
    }

    public function canUpdate(): bool
    {
        return
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || (
                $this->customer_id == auth()?->user()?->customer?->id
                && $this->clinic->isAvailable()
                && in_array($this->status, [AppointmentStatusEnum::PENDING->value, AppointmentStatusEnum::BOOKED->value])
            )
            || auth()->user()?->isAdmin();
    }

    public function cancelLog(): HasOne
    {
        return $this->hasOne(AppointmentLog::class)
            ->where('status', AppointmentStatusEnum::CANCELLED->value)
            ->latestOfMany();
    }
}
