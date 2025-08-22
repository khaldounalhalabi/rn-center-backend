<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends  BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository
{
    protected string $modelClass = Service::class;

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('clinic_id', $clinicId)
        );
    }

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(isDoctor(), function (Builder $builder) {
                $builder->where('clinic_id', clinic()?->id);
            });
    }

    /**
     * @return Collection<Service>|Service[]
     */
    public function getClinicServicesNames(): Collection|array
    {
        return $this->globalQuery()->select(['name', 'id'])->where('clinic_id', clinic()?->id)->get();
    }

    public function getByCategory(int $categoryId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('service_category_id', $categoryId)
        );
    }
}
