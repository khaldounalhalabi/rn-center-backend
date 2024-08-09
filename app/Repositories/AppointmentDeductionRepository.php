<?php

namespace App\Repositories;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Models\AppointmentDeduction;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as CollectionAlias;
use Illuminate\Support\Facades\Log;
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
        $perPage = request('per_page', $perPage);
        $data = $this->globalQuery($relations, $countable)
            ->where('clinic_id', $clinicId)
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data),
            ];
        }
        return null;
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
                $deductions->each(function (AppointmentDeduction $deduction) use ($callable) {
                    $callable($deduction);
                });
            });
    }
}
