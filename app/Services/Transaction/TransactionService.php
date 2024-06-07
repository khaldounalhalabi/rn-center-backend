<?php

namespace App\Services\Transaction;

use App\Enums\RolesPermissionEnum;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\BaseService;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ITransactionService<Transaction>
 * Class UserService
 */
class TransactionService extends BaseService implements ITransactionService
{
    /**
     * TransactionService constructor.
     * @param TransactionRepository $repository
     */
    public function __construct(TransactionRepository $repository)
    {
        parent::__construct($repository);
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser?->hasRole(RolesPermissionEnum::ADMIN['role'])) {
            return null;
        }
        $data['actor_id'] = $authUser->id;
        return parent::store($data, $relationships, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser?->hasRole(RolesPermissionEnum::ADMIN['role'])) {
            return null;
        }
        $data['actor_id'] = $authUser->id;
        return parent::update($data, $id, $relationships, $countable);
    }
}
