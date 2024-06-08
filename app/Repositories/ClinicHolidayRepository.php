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

}
