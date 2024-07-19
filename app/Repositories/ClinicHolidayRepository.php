<?php

namespace App\Repositories;

use App\Models\ClinicHoliday;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<ClinicHoliday>
 */
class ClinicHolidayRepository extends BaseRepository
{
    protected string $modelClass = ClinicHoliday::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered, function (Builder $query) {
                $query->whereHas('clinic', function (Builder $q) {
                    $q->available();
                });
            });
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return array|null
     */
    public function getClinicHolidays($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->where('clinic_id', $clinicId)
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
