<?php

namespace App\Repositories;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Models\AppointmentDeduction;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as CollectionAlias;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<AppointmentDeduction>
 */
class AppointmentDeductionRepository extends BaseRepository
{
    protected string $modelClass = AppointmentDeduction::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        $query = parent::globalQuery($relations, $countable)
            ->when(auth()->user()?->isClinic(), function (Builder $query) {
                $query->where('clinic_id', auth()->user()?->getClinicId());
            });
        return $query;
    }

    public function export(array $ids = null): BinaryFileResponse
    {
        $year = request('year', now()->year);
        $month = request('month', now()->monthName);
        $date = Carbon::parse("$month-$year");
        $ids = $this->globalQuery()
            ->where('date', '>=', $date->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', $date->lastOfMonth()->format('Y-m-d'))
            ->get()
            ->pluck('id')
            ->toArray();
        return parent::export($ids);
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery($this->globalQuery($relations, $countable)
            ->where('clinic_id', $clinicId));
    }

    /**
     * @param string $year
     * @param string $month
     * @param array  $relations
     * @param array  $countable
     * @return Collection<AppointmentDeduction>|AppointmentDeduction[]
     */
    public function getPendingByYearAndMonth(string $year, string $month, array $relations = [], array $countable = []): Collection|array
    {
        $date = Carbon::parse("$month-$year");
        return $this->globalQuery($relations, $countable)
            ->where('date', '>=', $date->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', $date->lastOfMonth()->format('Y-m-d'))
            ->where('status', AppointmentDeductionStatusEnum::PENDING->value)
            ->get();
    }

    public function getDoneDeductions(array $relations = [], array $countable = []): Collection|array
    {
        return $this->globalQuery($relations, $countable)
            ->where('status', AppointmentDeductionStatusEnum::DONE->value)
            ->get();
    }

    /**
     * @param array $relations
     * @param array $countable
     * @return Collection<AppointmentDeduction>|AppointmentDeduction[]
     */
    public function getPendingDeductions(array $relations = [], array $countable = []): Collection|array
    {
        return $this->globalQuery($relations, $countable)
            ->where('status', AppointmentDeductionStatusEnum::PENDING->value)
            ->get();
    }

    public function bulk(\Closure $callable, array $ids = []): void
    {
        $this->globalQuery()->whereIn('id', $ids)
            ->chunk(10, function (CollectionAlias $deductions) use ($callable) {
                $deductions->each(fn(AppointmentDeduction $deduction) => $callable($deduction));
            });
    }

    public function deductionsSummedByMonth()
    {
        $year = request('year', now()->year);
        return $this->globalQuery()
            ->selectRaw("SUM(amount) as earnings, DATE_FORMAT(date,'%Y-%M') as formatted_date")
            ->whereRaw("YEAR(date) = $year")
            ->where('status', AppointmentDeductionStatusEnum::DONE->value)
            ->groupByRaw("formatted_date")
            ->get()
            ->sortByDesc('formatted_date')
            ->map(function ($deduction) {
                return [
                    'earnings' => $deduction->earnings,
                    'date'     => Carbon::parse($deduction->formatted_date)->format('Y-m'),
                ];
            });
    }

    /**
     * @param       $clinicId
     * @param array $date
     * @return Collection<AppointmentDeduction>|AppointmentDeduction[]
     */
    public function getByDateRange($clinicId, $startDate, $endDate): Collection|array
    {
        return $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get();
    }
}
