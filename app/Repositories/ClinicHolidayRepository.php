<?php

namespace  App\Repositories;

use App\Models\ClinicHoliday;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<ClinicHoliday>
 */
class ClinicHolidayRepository extends BaseRepository
{
    public function __construct(ClinicHoliday $clinicHoliday)
    {
        parent::__construct($clinicHoliday);
    }
}
