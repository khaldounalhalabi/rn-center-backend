<?php

namespace App\Services\v1\Vacation;

use App\Models\Vacation;
use App\Repositories\VacationRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Vacation>
 * @property VacationRepository $repository
 */
class VacationService extends BaseService
{
    use Makable;

    protected string $repositoryClass = VacationRepository::class;
}
