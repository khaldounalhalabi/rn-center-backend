<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Transaction>
 * @property TransactionRepository $repository
 */
class TransactionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = TransactionRepository::class;
}
