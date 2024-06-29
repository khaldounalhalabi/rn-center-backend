<?php

namespace App\Services;

use App\Models\AppointmentDeduction;
use App\Repositories\AppointmentDeductionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<AppointmentDeduction>
 * @property AppointmentDeductionRepository $repository
 */
class AppointmentDeductionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentDeductionRepository::class;

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        $deduction = $this->repository->find($id, $relationships, $countable);
        if (!$deduction?->canShow()) {
            return null;
        }

        return $deduction;
    }
}
