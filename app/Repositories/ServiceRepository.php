<?php

namespace App\Repositories;

use App\Enums\ServiceStatusEnum;
use App\Models\Clinic;
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

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(auth()->user()?->isClinic(), function (Builder $builder) {
                $builder->where('clinic_id', auth()->user()?->getClinicId());
            })->when($this->filtered, function (Builder $q) {
                $q->where('status', ServiceStatusEnum::ACTIVE->value);
            })->when(!auth()->user()?->isAdmin() && !auth()->user()?->isClinic(), function (Builder|Service $b) {
                $b->where('status', ServiceStatusEnum::ACTIVE->value)
                    ->whereHas('clinic', function (Builder|Clinic $q2) {
                        $q2->available()->online();
                    });
            });
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->where('clinic_id', $clinicId)
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data),
            ];
        }
        return null;
    }

    /**
     * @return Collection<Service>|Service[]
     */
    public function getClinicServicesNames(): Collection|array
    {
        return $this->globalQuery()->select(['name', 'id'])->where('clinic_id', auth()->user()?->getClinicId())->get();
    }
}
