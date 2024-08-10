<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Review>
 * @property ReviewRepository $repository
 */
class ReviewService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ReviewRepository::class;

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }
}
