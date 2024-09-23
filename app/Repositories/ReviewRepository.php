<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Review;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Review>
 */
class ReviewRepository extends BaseRepository
{
    protected string $modelClass = Review::class;

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('clinic_id', $clinicId)
                ->whereHas('clinic', function (Builder|Clinic $q) {
                    $q->online()->available();
                })
        );
    }
}
