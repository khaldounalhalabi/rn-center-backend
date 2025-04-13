<?php

namespace App\Services;

use App\Models\ClinicHoliday;
use App\Repositories\ClinicHolidayRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<ClinicHoliday>
 * @property ClinicHolidayRepository $repository
 */
class ClinicHolidayService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicHolidayRepository::class;

    public function getCurrentClinicHolidays(array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        if (!isDoctor()) {
            return null;
        }

        return $this->repository->getClinicHolidays(clinic()?->id, $relations, $countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var ClinicHoliday $holiday */
        $holiday = parent::view($id, $relationships, $countable);

        if ($holiday?->canShow()) {
            return $holiday;
        }

        return null;
    }


    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $holiday = $this->repository->find($id);

        if (!$holiday?->canUpdate()) {
            return null;
        }

        return $this->repository->update($data, $holiday, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $holiday = $this->repository->find($id);

        if (!$holiday?->canDelete()) {
            return null;
        }

        return $holiday->delete();
    }
}
