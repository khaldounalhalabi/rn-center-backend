<?php

namespace App\Services;

use App\Models\Medicine;
use App\Repositories\MedicineRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Medicine>
 * @property MedicineRepository $repository
 */
class MedicineService extends BaseService
{
    use Makable;

    protected string $repositoryClass = MedicineRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $medicine = $this->repository->getByName($data['name']);

        if ($medicine) {
            return $this->repository->update($data, $medicine, $relationships, $countable);
        }

        if (isset($data['barcode'])) {
            $medicine = $this->repository->getByBarcode($data['barcode']);
        }

        if ($medicine) {
            return $this->repository->update($data, $medicine, $relationships, $countable);
        }

        return $this->repository->create($data, $relationships, $countable);
    }
}
