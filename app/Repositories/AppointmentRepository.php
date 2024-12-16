<?php

namespace App\Repositories;

use App\Enums\AppointmentStatusEnum;
use App\Excel\BaseExporter;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<Appointment>
 */
class AppointmentRepository extends BaseRepository
{
    protected string $modelClass = Appointment::class;

    protected function orderQueryBy(Builder $query, bool $defaultOrder = true, ?array $defaultCols = null): Builder
    {
        return parent::orderQueryBy($query, $defaultOrder, [
            'appointment_sequence' => 'asc',
            'created_at' => 'desc'
        ]);
    }

    /**
     * @param array $relations
     * @param array $countable
     * @param bool  $defaultOrder
     * @return Builder|Appointment
     */
    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered, function (Builder $query) {
                $query->whereHas('customer', function (Builder $q) {
                    $q->available();
                })->whereHas('clinic', function (Builder $builder) {
                    $builder->available();
                });
            })->when(auth()->user()?->isClinic(), function (Builder $query) {
                $query->where('clinic_id', auth()->user()?->getClinicId());
            })->when(auth()->user()?->isCustomer(), function (Builder $query) {
                $query->where('customer_id', auth()->user()?->customer?->id);
            });
    }

    /**
     * @param             $clinicId
     * @param string|null $date
     * @return Appointment|null
     */
    public function getClinicLastAppointmentInDay($clinicId, ?string $date = null): null|Appointment
    {
        if (!$date) $date = now()->format('Y-m-d');

        return $this->globalQuery()
            ->where('date', $date)
            ->where('clinic_id', $clinicId)
            ->orderBy('appointment_sequence', 'DESC')
            ->first();
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    public function getByClinic($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery($this->globalQuery($relations)
            ->where('clinic_id', $clinicId));
    }

    /**
     * @param                        $clinicId
     * @param                        $appointmentSequence
     * @param string|Carbon|DateTime $date
     * @param array                  $data
     * @return bool|int
     */
    public function updatePreviousCheckinClinicAppointments($clinicId, $appointmentSequence, string|Carbon|DateTime $date, array $data): bool|int
    {
        $data = $this->unsetNullable($data);
        return $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('date', $date)
            ->where('status', AppointmentStatusEnum::CHECKIN->value)
            ->where('appointment_sequence', '<', $appointmentSequence)
            ->chunk(5, function (Collection $appointments) use ($data) {
                foreach ($appointments as /** @var Appointment $appointment */ $appointment) {
                    $appointment->updateQuietly($data);
                    AppointmentLog::create([
                        'cancellation_reason' => $data['cancellation_reason'] ?? "",
                        'status' => $data['status'] ?? $appointment->status,
                        'happen_in' => now(),
                        'appointment_id' => $appointment->id,
                        'actor_id' => auth()->user()?->id,
                        'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
                        'event' => "appointment status has been changed to {$data['status']} in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en,
                    ]);
                }
            });
    }

    /**
     * @param int      $customerId
     * @param int|null $clinicId
     * @param array    $relations
     * @param array    $countable
     * @return Appointment|null
     */
    public function getCustomerLastAppointment(int $customerId, ?int $clinicId = null, array $relations = [], array $countable = []): ?Appointment
    {
        return $this->globalQuery($relations, $countable)
            ->where('customer_id', $customerId)
            ->when($clinicId, fn(Builder $query) => $query->where('clinic_id', $clinicId))
            ->orderBy('date', 'DESC')
            ->where('status', AppointmentStatusEnum::CHECKOUT->value)
            ->first();
    }

    public function export(array $ids = null): BinaryFileResponse
    {
        $year = request('year', now()->year);
        $month = request('month', now()->monthName);
        $date = Carbon::parse("$month-$year");
        $collection = $this->globalQuery()
            ->where('date', '>=', $date->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', $date->lastOfMonth()->format('Y-m-d'))
            ->when(auth()->user()?->isClinic(), function (Builder $query) {
                $query->where('clinic_id', auth()->user()?->getClinicId());
            })
            ->get();
        $requestedColumns = request("columns") ?? null;
        return Excel::download(
            new BaseExporter($collection, $this->model, $requestedColumns),
            $this->model->getTable() . ".xlsx",
        );
    }

    public function getByDate($date, $customerId = null, $clinicId = null, array $relations = [], array $countable = [])
    {
        return $this->globalQuery($relations, $countable)
            ->where('date', $date)
            ->when(isset($customerId), function (Builder $query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->when(isset($clinicId), function (Builder $query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })->get();
    }

    public function getByCustomer($customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery($this->globalQuery($relations, $countable)
            ->where('customer_id', $customerId));
    }

    public function getTodayAppointments($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery($this->globalQuery($relations, $countable)
            ->where('date', now()->format('Y-m-d'))
            ->where('clinic_id', $clinicId));
    }

    public function getByClinicDayRange(Clinic $clinic, array $relations = [], array $countable = []): array|EloquentCollection|\LaravelIdea\Helper\App\Models\_IH_Appointment_C
    {
        return $this->globalQuery($relations, $countable)
            ->validNotEnded()
            ->where('clinic_id', $clinic->id)
            ->whereBetween(
                'date',
                [
                    now()->format('Y-m-d'),
                    now()->addDays(
                        ($clinic->appointment_day_range ?? 0) > 0
                            ? $clinic->appointment_day_range - 1
                            : 0
                    )->format('Y-m-d'),
                ])
            ->get();
    }

    /**
     * @return Collection
     */
    public function appointmentsCountInMonth(): Collection
    {
        $year = request('year', now()->year);
        return $this->globalQuery()->selectRaw("COUNT(*) as appointment_count, DATE_FORMAT(date,'%Y-%M') as formatted_date")
            ->whereRaw("YEAR(date) = $year")
            ->groupByRaw("formatted_date")
            ->get()->map(function (Appointment $appointment) {
                return [
                    'appointment_count' => $appointment->appointment_count,
                    'date' => Carbon::parse($appointment->formatted_date)->format('Y-m'),
                ];
            });
    }

    /**
     * @return Appointment[]|EloquentCollection<Appointment>|Collection<Appointment>
     */
    public function getAllCompletedInCountInMonth(): array|EloquentCollection|Collection
    {
        $year = request('year', now()->year);
        return $this->globalQuery()
            ->selectRaw("COUNT(*) as appointment_count, DATE_FORMAT(date,'%Y-%M') as formatted_date")
            ->whereRaw("YEAR(date) = $year")
            ->where('status', AppointmentStatusEnum::CHECKOUT->value)
            ->groupByRaw("formatted_date")
            ->get()
            ->sortByDesc('formatted_date')
            ->map(function (Appointment $appointment) {
                return [
                    'appointment_count' => $appointment->appointment_count,
                    'date' => Carbon::parse($appointment->formatted_date)->format('Y-m'),
                ];
            });
    }

    public function recentAppointments(array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('date', '>=', now()->subDays(3)->format('Y-m-d'))
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where('status', '!=', AppointmentStatusEnum::CANCELLED->value)
        );
    }

    public function codeExists($uniqueCode): bool
    {
        return Appointment::withoutGlobalScopes()->where('appointment_unique_code', $uniqueCode)->exists();
    }
}
