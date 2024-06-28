<?php

namespace App\Repositories;

use App\Models\AppointmentDeduction;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<AppointmentDeduction>
 */
class AppointmentDeductionRepository extends BaseRepository
{
    protected string $modelClass = AppointmentDeduction::class;
}
