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

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when(auth()->user()?->isClinic(), function (Builder $builder) {
                $builder->where('clinic_id', auth()->user()?->getClinicId());
            })->when(auth()->user()?->isCustomer(), function (Builder|Prescription $prescription) {
                $prescription->where('customer_id', auth()->user()?->customer?->id);
            });
    }


    /**
     * @param int   $appointmentId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    public function getByAppointmentId(int $appointmentId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations)->where('appointment_id', $appointmentId)
        );
    }

    public function getClinicCustomerPrescriptions($clinicId, $customerId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
                ->where('clinic_id', $clinicId)
        );
    }
}
