<?php

namespace App\Services\v1\Holiday;

use App\Models\Holiday;
use App\Repositories\HolidayRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Holiday>
 * @property HolidayRepository $repository
 */
class HolidayService extends BaseService
{
    use Makable;

    protected string $repositoryClass = HolidayRepository::class;
}
