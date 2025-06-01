<?php

namespace App\Services\v1\MedicalRecord;

use App\Models\MedicalRecord;
use App\Repositories\MedicalRecordRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<MedicalRecord>
 * @property MedicalRecordRepository $repository
 */
class MedicalRecordService extends BaseService
{
    use Makable;

    protected string $repositoryClass = MedicalRecordRepository::class;

    public function getByCustomer(int $customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByCustomer($customerId, $relations, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $record = $this->repository->find($id);
        if (!$record?->canUpdate()) {
            return null;
        }

        return $this->repository->update($data, $record, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $record = $this->repository->find($id);

        if (!$record?->canDelete()) {
            return false;
        }

        return $this->repository->delete($record);
    }
}
