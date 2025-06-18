<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Clinic>
 */
class ClinicRepository extends BaseRepository
{
    protected string $modelClass = Clinic::class;
}
