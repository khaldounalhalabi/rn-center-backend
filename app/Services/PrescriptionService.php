<?php

namespace App\Services;

use App\Models\Prescription;
use App\Repositories\MedicinePrescriptionRepository;
use App\Repositories\PrescriptionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Prescription>
 * @property PrescriptionRepository $repository
 */
class PrescriptionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PrescriptionRepository::class;
    private MedicinePrescriptionRepository $medicinePrescriptionRepository;


    public function init(): void
    {
        parent::__construct();
        $this->medicinePrescriptionRepository = MedicinePrescriptionRepository::make();
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
                'medicine_id'     => $item['medicine_id'],
                'dosage'          => $item['dosage'] ?? "",
                'duration'        => $item['duration'] ?? "",
                'time'            => $item['time'] ?? "",
                'dose_interval'   => $item['dose_interval'] ?? "",
                'comment'         => $item['comment'] ?? "",
                'created_at'      => now(),
                'updated_at'      => now(),
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
            $prescription->medicines()->detach();
            $medicineData = $data['medicines'];
            $medicines = collect();

            foreach ($medicineData as $item) {
                $medicines->push([
                    'prescription_id' => $prescription->id,
                    'medicine_id'     => $item['medicine_id'],
                    'dosage'          => $item['dosage'] ?? "",
                    'duration'        => $item['duration'] ?? "",
                    'time'            => $item['time'] ?? "",
                    'dose_interval'   => $item['dose_interval'] ?? "",
                    'comment'         => $item['comment'] ?? "",
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            $this->medicinePrescriptionRepository->insert($medicines->toArray());
        }

        return $prescription->load($relationships)->loadCount($countable);
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

    public function delete($id): ?bool
    {
        $prescription = $this->repository->find($id);

        if (!$prescription || !$prescription?->canShow()) {
            return null;
        }

        return $prescription->delete();
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
