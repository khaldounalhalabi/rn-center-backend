<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AppointmentDeductionRepository;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Transaction>
 * @property TransactionRepository $repository
 */
class TransactionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = TransactionRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser?->isAdmin()) {
            return null;
        }
        $data['actor_id'] = $authUser->id;
        return parent::store($data, $relationships, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser?->isAdmin()) {
            return null;
        }
        $data['actor_id'] = $authUser->id;
        return parent::update($data, $id, $relationships, $countable);
    }

    public function summary(): array
    {
        $data['pending_amount'] = AppointmentDeductionRepository::make()
            ->getPendingDeductions()
            ->sum('amount');

        $data['balance'] = auth()->user()?->balance()?->balance ?? 0;
        return $data;
    }
}
