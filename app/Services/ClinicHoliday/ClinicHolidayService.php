<?php

namespace App\Services\ClinicHoliday;

use App\Models\ClinicHoliday;
use App\Services\Contracts\BaseService;
use App\Repositories\ClinicHolidayRepository;

/**
 * @implements IClinicHolidayService<ClinicHoliday>
 * Class UserService
 */
class ClinicHolidayService extends BaseService implements IClinicHolidayService
{
    /**
     * ClinicHolidayService constructor.
     *
     * @param ClinicHolidayRepository $repository
     */
    public function __construct(ClinicHolidayRepository $repository)
    {
        parent::__construct($repository);
    }
}
