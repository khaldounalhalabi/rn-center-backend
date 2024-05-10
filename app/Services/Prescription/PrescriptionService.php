<?php

namespace App\Services\Prescription;

use App\Models\Prescription;
use App\Repositories\MedicinePrescriptionRepository;
use App\Services\Contracts\BaseService;
use App\Repositories\PrescriptionRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements IPrescriptionService<Prescription>
 * @extends BaseService<Prescription>
 * Class UserService
 */
class PrescriptionService extends BaseService implements IPrescriptionService
{
    private MedicinePrescriptionRepository $medicinePrescriptionRepository;

    /**
     * PrescriptionService constructor.
     * @param PrescriptionRepository         $repository
     * @param MedicinePrescriptionRepository $medicinePrescriptionRepository
     */
    public function __construct(PrescriptionRepository         $repository,
                                MedicinePrescriptionRepository $medicinePrescriptionRepository)
    {
        parent::__construct($repository);
        $this->medicinePrescriptionRepository = $medicinePrescriptionRepository;
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        $prescription = $this->repository->find($id, $relationships, $countable);

        if (!$prescription || !$prescription?->canShow()) {
            return null;
        }

        return $prescription;
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $prescription = $this->repository->create($data);

        if (!isset($data['medicines'])) {
            return null;
        }

        $medicineData = $data['medicines'];
        $medicines = collect();

        foreach ($medicineData as $item) {
            $medicines->push([
                'prescription_id' => $prescription->id,
                ...$item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->medicinePrescriptionRepository->insert($medicines->toArray());

        return $prescription->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $prescription = $this->repository->find($id);

        if (!$prescription) {
            return null;
        }

        if (!$prescription->canUpdate()) {
            return null;
        }

        $this->repository->update($data, $prescription);

        if (isset($data['medicines'])) {
            $medicineData = $data['medicines'];
            $medicines = collect();

            foreach ($medicineData as $item) {
                $medicines->push([
                    'prescription_id' => $prescription->id,
                    ...$item,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->medicinePrescriptionRepository->insert($medicines->toArray());
        }

        return $prescription->load($relationships)->loadCount($countable);
    }

    public function delete($id): ?bool
    {
        $prescription = $this->repository->find($id);

        if (!$prescription || !$prescription?->canShow()) {
            return null;
        }

        return $prescription->delete();
    }

    /**
     * @param int $medicineDataId
     * @return bool|null
     */
    public function removeMedicine(int $medicineDataId): ?bool
    {
        $medicineData = $this->medicinePrescriptionRepository->find($medicineDataId);

        if (!$medicineData) {
            return null;
        }

        if (!$medicineData->prescription->canDelete()) {
            return null;
        }

        return $this->medicinePrescriptionRepository->delete($medicineDataId);
    }

    /**
     * @param int   $appointmentId
     * @param array $relations
     * @param int   $perPage
     * @return null|array{data:mixed , pagination_data:array}
     */
    public function getByAppointmentId(int $appointmentId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->repository->getByAppointmentId($appointmentId, $relations, $perPage);
    }
}
