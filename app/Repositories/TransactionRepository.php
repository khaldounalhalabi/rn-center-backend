<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Transaction>
 */
class TransactionRepository extends BaseRepository
{
    protected string $modelClass = Transaction::class;
}
