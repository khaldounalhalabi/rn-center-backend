<?php

namespace App\Repositories;

use App\Models\Speciality;
use App\Repositories\Contracts\BaseRepository;
use LaravelIdea\Helper\App\Models\_IH_Speciality_C;

/**
 * @extends  BaseRepository<Speciality>
 * <Speciality>
 */
class SpecialityRepository extends BaseRepository
{
    protected string $modelClass = Speciality::class;


    /**
     * @param array<integer> $ids
     * @return _IH_Speciality_C|array
     */
    public function getAllWithIds(array $ids = []): _IH_Speciality_C|array
    {
        return Speciality::whereIn('id', $ids)->get();
    }

    public function getOrderedByClinicsCount(array $relations = [], array $countable = []): ?array
    {
        $data = $this->globalQuery($relations, $countable , false)
            ->withCount('clinics')
            ->orderByDesc('clinics_count')
            ->paginate($this->perPage);

        if ($data->count()) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
