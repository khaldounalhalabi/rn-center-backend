<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Customer>
 */
class CustomerRepository extends BaseRepository
{
    protected string $modelClass = Customer::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations, $countable)
            ->when($this->filtered, function (Builder $query) {
                $query->available();
            });
    }

    public function getByUserId($userId): ?Customer
    {
        return $this->globalQuery()->where('user_id', $userId)->first();
    }

    public function getClinicCustomers($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->whereHas('patientProfiles', function (Builder $query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })->orWhereHas('appointments', function (Builder $q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })->paginate($perPage);

        if ($data?->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data),
            ];
        }

        return null;
    }

    public function getRecent(array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('created_at', '>=', now()->subDays(3)->format('Y-m-d'))
                ->where('created_at', '<=', now()->format('Y-m-d'))
        );
    }
}
