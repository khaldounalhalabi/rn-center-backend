<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Models\Transaction;
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
}
