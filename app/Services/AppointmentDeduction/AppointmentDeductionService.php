<?php

namespace App\Services\AppointmentDeduction;

use App\Models\AppointmentDeduction;
use App\Repositories\AppointmentDeductionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<AppointmentDeduction>
 * @property AppointmentDeductionRepository $repository
 */
class AppointmentDeductionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentDeductionRepository::class;
}
