<?php

namespace App\Repositories;

use App\Models\Holiday;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Holiday>
 */
class HolidayRepository extends BaseRepository
{
    protected string $modelClass = Holiday::class;
}
