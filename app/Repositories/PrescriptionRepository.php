<?php

namespace App\Repositories;

use App\Models\Prescription;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Prescription>
 */
class PrescriptionRepository extends BaseRepository
{
    protected string $modelClass = Prescription::class;

    /**
     * @param int   $appointmentId
     * @param array $relations
     * @return array|null
     */
    public function getByAppointmentId(int $appointmentId, array $relations = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations)->where('appointment_id', $appointmentId)
        );
    }

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(isDoctor(), function (Builder $builder) {
                $builder->where('clinic_id', clinic()?->id);
            })->when(isCustomer(), function (Builder|Prescription $prescription) {
                $prescription->where('customer_id', auth()->user()?->customer?->id);
            });
    }

    public function getClinicCustomerPrescriptions($clinicId, $customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
                ->where('clinic_id', $clinicId)
        );
    }
}
