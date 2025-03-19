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

    public function view($id, array $relationships = [], array $countable = []): ?Service
    {
        $service = parent::view($id, $relationships, $countable);

        if ($service?->canShow()) {
            return $service;
        }

        return null;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $service = $this->repository->find($id);

        if (!$service?->canUpdate()) {
            return null;
        }

        return $this->repository->update($data, $service, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $service = $this->repository->find($id);

        if (!$service?->canDelete()) {
            return null;
        }

        $service->delete();
        return true;
    }

    public function getClinicServices($clinicId, array $relations = [], array $countable = [], $perPage = 10): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }

    /**
     * @return Collection<Service>|Service[]
     */
    public function getClinicServicesNames(): Collection|array
    {
        return $this->repository->getClinicServicesNames();
    }
}
