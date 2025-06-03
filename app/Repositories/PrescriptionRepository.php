<?php

namespace App\Repositories;

use App\Models\Customer;
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
            ->when(isCustomer(), function (Builder|Prescription $prescription) {
                $prescription->where('customer_id', auth()->user()?->customer?->id);
            });
    }

    public function getByCustomer($customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('customer_id', $customerId)
                ->when(isDoctor(), function (Builder|Prescription $prescription) {
                    $prescription->whereHas('customer', function (Builder|Customer $customer) {
                        $customer->byClinic(clinic()?->id);
                    });
                })
        );
    }
}
