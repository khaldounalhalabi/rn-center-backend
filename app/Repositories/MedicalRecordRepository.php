<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends  BaseRepository<MedicalRecord>
 */
class MedicalRecordRepository extends BaseRepository
{
    protected string $modelClass = MedicalRecord::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder|Model
    {
        return parent::globalQuery($relations, $countable, $defaultOrder)
            ->whereHas('customer', function (Builder|Customer $customer) {
                $customer->when(isDoctor(), function (Builder|Customer $customer) {
                    $customer->byClinic(clinic()?->id);
                });
            })->when(isCustomer(), function (Builder|MedicalRecord $medicalRecord) {
                $medicalRecord->where('customer_id', customer()?->id);
            });
    }

    public function getByCustomer(int $customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->whereHas('customer', function (Builder|Customer $customer) use ($customerId) {
                    $customer->where('customers.id', $customerId);
                })
        );
    }
}
