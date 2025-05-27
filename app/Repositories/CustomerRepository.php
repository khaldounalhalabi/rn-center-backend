<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends  BaseRepository<Customer>
 */
class CustomerRepository extends BaseRepository
{
    protected string $modelClass = Customer::class;

    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder|Model
    {
        return parent::globalQuery($relations, $countable, $defaultOrder)
            ->when(isDoctor(), function (Builder|Customer $query) {
                $query->byClinic(clinic()?->id);
            });
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
