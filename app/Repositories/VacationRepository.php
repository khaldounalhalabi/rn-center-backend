<?php

namespace App\Repositories;

use App\Models\Vacation;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Vacation>
 */
class VacationRepository extends BaseRepository
{
    protected string $modelClass = Vacation::class;
}
