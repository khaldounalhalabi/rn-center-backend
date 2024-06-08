<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Notifications\Customer\AppointmentRemainingTimeNotification;
use App\Services\Notification\FirebaseServices;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property integer  customer_id
 * @property integer  clinic_id
 * @property string   note
 * @property integer  service_id
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
 */
class Appointment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        parent::booted();
        self::creating(function (Appointment $appointment) {
            self::handleRemainingTime($appointment);
        });
    }

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'note',
        'service_id',
        'extra_fees',
        'total_cost',
        'type',
        'date',
        'status',
        'device_type',
        'appointment_sequence',
        'qr_code',
        'remaining_time'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d:',
    ];

    public function exportable(): array
    {
        return [
            'note',
            'extra_fees',
            'total_cost',
            'type',
            'date',
            'from',
            'to',
            'status',
            'device_type',
            'appointment_sequence',
            'qr_code',
            'clinic.name',
            'service.name',
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
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    #[ArrayShape(['customer.user' => "string[]", 'clinic' => "string[]", 'clinic.user' => "string[]"])]
    public static function relationsSearchableArray(): array
    {
        return [
            'customer.user' => [
                'first_name',
                'last_name',
                'middle_name',
                'full_name',
            ],
            'clinic'        => [
                'name',
            ],
            'clinic.user'   => [
                'first_name',
                'last_name',
                'middle_name',
                'full_name'
            ]
        ];
    }

    public function canUpdate(): bool
    {
        return auth()->user()->hasRole(RolesPermissionEnum::ADMIN['role'])
            || (auth()->user()->id == $this->clinic->user_id);
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
                'name'     => 'from',
                'method'   => 'whereTime',
                'operator' => '>=',
            ],
            [
                'name'     => 'to',
                'method'   => 'whereTime',
                'operator' => '<=',
            ],
        ];
    }

    public function appointmentLogs(): HasMany
    {
        return $this->hasMany(AppointmentLog::class);
    }

    #[ArrayShape(['clinic.user.first_name' => "\Closure", 'customer.user.first_name' => "\Closure"])]
    public function customOrders(): array
    {
        return [
            'clinic.user.first_name'   => function (Builder $query, $dir) {
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
            }
        ];
    }

    /**
     * @param Appointment $appointment
     * @return Appointment
     */
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

            $appointment_gap = $appointment->clinic->schedules->pluck('appointment_gap')->unique()->first();
            $approximate_appointment_time = $appointment->clinic->approximate_appointment_time;
            $diffDays = $appointment->date->diffInDays(now()->format('Y-m-d'));
            $diffMinutes = ($approximate_appointment_time + $appointment_gap) * $beforeAppointmentsCount;

            try {
                $diffTime = CarbonInterval::hours(intdiv($diffMinutes, 60))->minutes($diffMinutes % 60)->forHumans();
            } catch (Exception) {
                $diffTime = intdiv($diffMinutes, 60) . " Hours  , " . $diffMinutes % 60 . " Minutes";
            }

            $appointment->remaining_time = $diffDays == 0
                ? "$diffTime , $beforeAppointmentsCount Patients Before You"
                : "$diffDays Days And $diffTime , $beforeAppointmentsCount Patients Before You";

            FirebaseServices::make()
                ->setData([
                    'remaining_time' => $appointment,
                    'message'        => "Your appointment booked in ($appointment->clinic->name) clinic in {$appointment->date} has an approximate time of : {$appointment->remaining_time}",
                    'appointment_id' => $appointment->id,
                    'clinic_id'      => $appointment->clinic_id,
                    // TODO::update open route for this when you do the customer pages or configure another way for handling the notification
                    'url'            => "#"
                ])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($appointment->customer->user)
                ->setNotification(AppointmentRemainingTimeNotification::class)
                ->send();
        }

        return $appointment;
    }

    public function scopeValidNotEnded(Builder $query): Builder
    {
        return $query->whereIn('status', [AppointmentStatusEnum::BOOKED->value, AppointmentStatusEnum::CHECKIN->value]);
    }
}
