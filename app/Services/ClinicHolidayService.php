<?php

namespace App\Services;

use App\Models\ClinicHoliday;
use App\Repositories\ClinicHolidayRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<ClinicHoliday>
 * @property ClinicHolidayRepository $repository
 */
class ClinicHolidayService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicHolidayRepository::class;
}
