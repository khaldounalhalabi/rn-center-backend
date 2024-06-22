<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository
{
    protected string $modelClass = Service::class;

    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        $query = parent::globalQuery($relations, $countable);

        return $query->when(auth()->user()?->isClinic(), function (Builder $query) {
            $query->where('clinic_id', auth()->user()?->getClinicId());
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
}
