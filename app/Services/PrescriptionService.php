<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
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

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        if (isset($data['appointment_id'])) {
            $appointment = AppointmentRepository::make()->find($data['appointment_id'], ['prescription']);
            if ($appointment?->prescription) {
                return null;
            }
        }

        $prescription = $this->repository->create($data);

        if ($prescription->next_visit) {
            $appointmentSequence = AppointmentService::make()->calculateAppointmentSequence($prescription->clinic_id, $prescription->next_visit->format('Y-m-d'));
            AppointmentRepository::make()->create([
                'clinic_id' => $prescription->clinic_id,
                'customer_id' => $prescription->customer_id,
                'status' => AppointmentStatusEnum::BOOKED->value,
                'date_time' => $prescription->next_visit,
                'total_cost' => $prescription->clinic?->appointment_cost,
                'appointment_sequence' => $appointmentSequence,
            ]);
        }

        $medicinePrescriptions = [];
        if (isset($data['medicines'])) {
            foreach ($data['medicines'] as $medicineData) {
                $medicinePrescriptions[] = [
                    ...$medicineData,
                    'prescription_id' => $prescription->id,
                ];
            }
        }

        MedicinePrescriptionRepository::make()->insert($medicinePrescriptions);

        return $prescription->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $prescription = $this->repository->update($data, $id);

        if (!$prescription) {
            return null;
        }

        $prescription->medicinePrescriptions()->delete();

        $medicinePrescriptions = [];
        if (isset($data['medicine_ids'])) {
            foreach ($data['medicine_ids'] as $medicineData) {
                $medicinePrescriptions[] = [
                    ...$medicineData,
                    'prescription_id' => $prescription->id,
                ];
            }
        }

        MedicinePrescriptionRepository::make()->insert($medicinePrescriptions);

        return $prescription->load($relationships)->loadCount($countable);
    }

    public function getClinicCustomerPrescriptions($customerId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getClinicCustomerPrescriptions($customerId, $relations, $countable);
    }
}
