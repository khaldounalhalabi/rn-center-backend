<?php

namespace App\Services\v1\MedicinePrescription;

use App\Enums\MedicinePrescriptionStatusEnum;
use App\Enums\MedicineStatusEnum;
use App\Models\MedicinePrescription;
use App\Repositories\MedicinePrescriptionRepository;
use App\Repositories\MedicineRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<MedicinePrescription>
 * @property MedicinePrescriptionRepository $repository
 */
class MedicinePrescriptionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = MedicinePrescriptionRepository::class;

    public function toggleStatus($medicinePrescriptionId): bool
    {
        $medPer = $this->repository->find($medicinePrescriptionId);
        if (!$medPer) {
            return false;
        }

        if ($medPer->status == MedicinePrescriptionStatusEnum::GIVEN->value) {
            $this->repository->update([
                'status' => MedicinePrescriptionStatusEnum::NOT_GIVEN->value,
            ], $medPer);
            $medicine = MedicineRepository::make()->find($medPer->medicine_id);
            MedicineRepository::make()->update([
                'quantity' => $medicine->quantity + 1,
            ], $medicine);
        } else {
            $this->repository->update([
                'status' => MedicinePrescriptionStatusEnum::GIVEN->value,
            ], $medPer);
            $medicine = MedicineRepository::make()->find($medPer->medicine_id);
            if ($medicine->quantity > 0 && $medicine->status == MedicineStatusEnum::EXISTS->value) {
                MedicineRepository::make()->update([
                    'quantity' => $medicine->quantity - 1,
                ], $medicine);
            }
        }

        return true;
    }
}
