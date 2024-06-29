<?php

namespace App\Repositories;

use App\Models\AppointmentDeduction;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<AppointmentDeduction>
 */
class AppointmentDeductionRepository extends BaseRepository
{
    protected string $modelClass = AppointmentDeduction::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(auth()->user()?->isClinic(), function (Builder $query) {
                $query->where('clinic_id', auth()->user()?->getClinicId());
            });
    }
}
