<?php

namespace App\Repositories;

use App\Models\ClinicHoliday;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<ClinicHoliday>
 */
class ClinicHolidayRepository extends BaseRepository
{
    protected string $modelClass = ClinicHoliday::class;

    /**
     * @param       $clinicId
     * @param array $relations
     * @param array $countable
     * @return array|null
     */
    public function getClinicHolidays($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('clinic_id', $clinicId)
        );
    }
}
