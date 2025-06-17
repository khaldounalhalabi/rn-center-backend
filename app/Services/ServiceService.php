<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
}
