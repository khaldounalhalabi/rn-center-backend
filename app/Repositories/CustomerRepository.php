<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Customer>
 * @implements IBaseRepository<Customer>
 */
class CustomerRepository extends BaseRepository
{
    protected string $modelClass = Customer::class;

    public function getByUserId($userId): ?Customer
    {
        return Customer::where('user_id', $userId)->first();
    }

    public function getClinicCustomers($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->whereHas('patientProfiles', function (Builder $query) use ($clinicId) {
            $query->where('clinic_id', $clinicId);
        })->paginate($perPage);

        if ($data?->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
