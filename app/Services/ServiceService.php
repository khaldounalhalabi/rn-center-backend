<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Service>
 * @property ServiceRepository $repository
 */
class ServiceService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ServiceRepository::class;

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }

    public function getByCategory(int $categoryId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByCategory($categoryId, $relations, $countable);
    }
}
