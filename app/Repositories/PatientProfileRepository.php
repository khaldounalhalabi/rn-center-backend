<?php

namespace App\Repositories;

use App\Models\PatientProfile;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<PatientProfile>
 */
class PatientProfileRepository extends BaseRepository
{
    protected string $modelClass = PatientProfile::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        return parent::globalQuery($relations , $countable)
            ->when($this->filtered, function (Builder $query) {
                $query->whereHas('customer', function (Builder $builder) {
                    $builder->available();
                })->whereHas('clinic', function (Builder $query) {
                    $query->available();
                });
            });
    }

    public function getByCustomerId($customerId, array $relations, array $countable = [], int $perPage = 10): ?array
    {
        $perPage = request('per_page') ?? $perPage;
        $data = $this->globalQuery($relations, $countable)
            ->where('customer_id', $customerId)
            ->paginate($perPage);

        if ($data->count()) {
            return [
                'data'            => $data->getCollection(),
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }

    public function getByClinicAndCustomer($clinicId, $customerId): ?PatientProfile
    {
        return PatientProfile::where('clinic_id', $clinicId)
            ->where('customer_id', $customerId)
            ->first();
    }
}
