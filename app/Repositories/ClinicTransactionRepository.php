<?php

namespace App\Repositories;

use App\Enums\ClinicTransactionStatusEnum;
use App\Models\ClinicTransaction;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<ClinicTransaction>
 */
class ClinicTransactionRepository extends BaseRepository
{
    protected string $modelClass = ClinicTransaction::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(auth()->user()?->isClinic(), function (Builder $query) {
                $query->where('clinic_id', auth()->user()?->getClinicId());
            });
    }

    public function export(array $ids = []): BinaryFileResponse
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

    /**
     * @param array $relations
     * @param array $countable
     * @return Collection<ClinicTransaction>|ClinicTransaction[]
     */
    public function getPendingTransactions(array $relations = [], array $countable = []): Collection|array
    {
        return $this->globalQuery($relations, $countable)
            ->where('status', ClinicTransactionStatusEnum::PENDING->value)
            ->get();
    }
}
