<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Customer;
use App\Models\PatientProfile;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<PatientProfile>
 */
class PatientProfileRepository extends BaseRepository
{
    protected string $modelClass = PatientProfile::class;

    public function getByCustomerId($customerId, array $relations, array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
        );
    }

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered || auth()->user()?->isCustomer(), function (Builder $query) {
                $query->whereHas('customer', function (Builder|Customer $builder) {
                    $builder->available();
                })->whereHas('clinic', function (Builder|Clinic $query) {
                    $query->available();
                });
            });
    }

    public function getByClinicAndCustomer($clinicId, $customerId): ?PatientProfile
    {
        return PatientProfile::where('clinic_id', $clinicId)
            ->where('customer_id', $customerId)
            ->first();
    }
}
