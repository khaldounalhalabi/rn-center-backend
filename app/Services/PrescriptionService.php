<?php

namespace App\Services;

use App\Models\Prescription;
use App\Repositories\AppointmentRepository;
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

        if (!$prescription?->canShow()) {
            return null;
        }

        return $prescription;
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        if (isset($data['appointment_id'])) {
            $appointment = AppointmentRepository::make()->find($data['appointment_id']);
            if (!$appointment?->canUpdate()) {
                return null;
            }
            $data['customer_id'] = $appointment->customer_id;
        }
        $prescription = $this->repository->create($data);

        if (!isset($data['medicines'])) {
            return null;
        }

        $this->createPrescriptionMedicines($data['medicines'], $prescription);

        return $prescription->load($relationships)->loadCount($countable);
    }

    /**
     * @param                         $medicines1
     * @param Prescription|null       $prescription
     * @return void
     */
    private function createPrescriptionMedicines($medicines1, Prescription|null $prescription): void
    {
        $medicineData = $medicines1;
        $medicines = collect();

        foreach ($medicineData as $item) {
            $medicines->push([
                'prescription_id' => $prescription->id,
                'medicine_id' => $item['medicine_id'],
                'dosage' => $item['dosage'] ?? "",
                'duration' => $item['duration'] ?? "",
                'time' => $item['time'] ?? "",
                'dose_interval' => $item['dose_interval'] ?? "",
                'comment' => $item['comment'] ?? "",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->medicinePrescriptionRepository->insert($medicines->toArray());
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $prescription = $this->repository->find($id);

        if (isset($data['appointment_id'])) {
            $appointment = AppointmentRepository::make()->find($data['appointment_id']);
            if (!$appointment?->canUpdate()) {
                return null;
            }
            $data['customer_id'] = $appointment->customer_id;
        }

        if (!$prescription?->canUpdate()) {
            return null;
        }

        $this->repository->update($data, $prescription);

        if (isset($data['medicines'])) {
            $prescription->medicines()->detach();
            $this->createPrescriptionMedicines($data['medicines'], $prescription);
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

        if (!$medicineData?->prescription?->canDelete()) {
            return null;
        }

        return $this->medicinePrescriptionRepository->delete($medicineDataId);
    }

    public function delete($id): ?bool
    {
        $prescription = $this->repository->find($id);

        if (!$prescription?->canShow()) {
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
        return $this->repository->getByAppointmentId($appointmentId, $relations);
    }

    public function getClinicCustomerPrescriptions($customerId, $clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getClinicCustomerPrescriptions($clinicId, $customerId, $relations, $countable);
    }
}
