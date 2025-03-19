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

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $medicine = $this->view($id);

        if (!$medicine) {
            return null;
        }

        return $this->repository->update($data, $medicine, $relationships, $countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Medicine
    {
        $medicine = parent::view($id, $relationships, $countable);

        if (!$medicine?->canShow()) {
            return null;
        }

        return $medicine;
    }

    public function delete($id): ?bool
    {
        $medicine = $this->view($id);

        if (!$medicine) {
            return null;
        }

        return parent::delete($id);
    }
}
