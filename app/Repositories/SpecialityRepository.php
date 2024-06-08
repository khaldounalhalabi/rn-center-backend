<?php

namespace App\Repositories;

use App\Models\Speciality;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use LaravelIdea\Helper\App\Models\_IH_Speciality_C;

/**
 * @extends  BaseRepository<Speciality>
 * @implements IBaseRepository<Speciality>
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
}
