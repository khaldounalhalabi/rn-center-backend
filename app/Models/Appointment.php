<?php

namespace App\Models;

use App\Casts\IntervalCast;
use App\Enums\AppointmentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $observables = [
        'statusChange'
    ];

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'service_id',
        'note',
        'extra_fees',
        'total_cost',
        'type',
        'date_time',
        'status',
        'appointment_sequence',
        'remaining_time',
        'discount',
    ];
    protected $casts = [
        'date_time' => 'datetime',
        'remaining_time' => IntervalCast::class,
    ];

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
            'date_time',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'customer.user' => [
                'full_name'
            ],
            'clinic.user' => [
                'full_name'
            ],
            'service' => [
                'name'
            ]
        ];
    }

    public static function handleRemainingTime(Appointment $appointment): Appointment
    {
        //TODO:: handle remaining time
        return $appointment;
    }

    public function exportable(): array
    {
        return [
            'customer.user.full_name',
            'clinic.user.full_name',
            'service.name',
            'note',
            'extra_fees',
            'total_cost',
            'type',
            'date_time',
            'status',
            'appointment_sequence',
            'remaining_time',
            'discount',
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
                'query' => fn(Builder|Appointment $query, $value) => $query->whereDate('date_time', Carbon::parse($value)->format('Y-m-d'))
            ],
            [
                'name' => 'service_id',
            ],
            [
                'name' => 'clinic_id'
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

    public function updateTotalCost(): static
    {
        $clinicCost = $this->clinic?->appointment_cost ?? 0;
        $serviceCost = $this->service?->price ?? 0;

        $this->updateQuietly([
            'total_cost' => $clinicCost + $serviceCost + $this->extra_fees - $this->discount,
        ]);

        return $this;
    }
}
