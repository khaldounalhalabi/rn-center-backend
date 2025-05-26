<?php

namespace App\Repositories;

use App\Excel\BaseExporter;
use App\Models\Appointment;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<Appointment>
 */
class AppointmentRepository extends BaseRepository
{
    protected string $modelClass = Appointment::class;

    /**
     * @param             $clinicId
     * @param string|null $date
     * @return Appointment|null
     */
    public function getClinicLastAppointmentInDay($clinicId, ?string $date = null): null|Appointment
    {
        if (!$date) $date = now()->format('Y-m-d');

        return $this->globalQuery()
            ->whereDate('date_time', $date)
            ->where('clinic_id', $clinicId)
            ->orderBy('appointment_sequence', 'DESC')
            ->first();
    }

    /**
     * @param array $relations
     * @param array $countable
     * @param bool  $defaultOrder
     * @return Builder|Appointment
     */
    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder|Appointment
    {
        return parent::globalQuery($relations, $countable, $defaultOrder)
            ->when(isDoctor(), function (Builder $query) {
                $query->where('clinic_id', clinic()?->id);
            })->when(isCustomer(), function (Builder $query) {
                $query->where('customer_id', customer()?->id);
            });
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(array $ids = null): BinaryFileResponse
    {
        $year = request('year', now()->year);
        $month = request('month', now()->monthName);
        $date = Carbon::parse("$month-$year");
        $collection = $this->globalQuery()
            ->whereDate('date_time', '>=', $date->firstOfMonth()->format('Y-m-d'))
            ->whereDate('date_time', '<=', $date->lastOfMonth()->format('Y-m-d'))
            ->when(isDoctor(), function (Builder $query) {
                $query->where('clinic_id', clinic()?->id);
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
            ->whereDate('date_time', $date)
            ->when(isset($customerId), function (Builder $query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->when(isset($clinicId), function (Builder $query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })->get();
    }

    /**
     * Get all appointments for a clinic on a specific date ordered by time
     * @param int         $clinicId
     * @param string|null $date
     * @param array       $relations
     * @return EloquentCollection<Appointment>
     */
    public function getClinicAppointmentsOrderedByTime(int $clinicId, ?string $date = null, array $relations = []): EloquentCollection
    {
        if (!$date) $date = now()->format('Y-m-d');

        return $this->globalQuery($relations)
            ->whereDate('date_time', $date)
            ->where('clinic_id', $clinicId)
            ->orderBy('date_time')
            ->get();
    }

    protected function orderQueryBy(Builder $query, bool $defaultOrder = true, ?array $defaultCols = null): Builder
    {
        return parent::orderQueryBy($query, $defaultOrder, [
            'appointment_sequence' => 'asc',
            'date_time' => 'desc'
        ]);
    }

    public function paginateByClinic(int $clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('clinic_id', $clinicId)
        );
    }

    public function paginateByCustomer(int $customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
        );
    }
}
