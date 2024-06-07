<?php

namespace  App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Transaction>
 */
class TransactionRepository extends BaseRepository
{
    public function __construct(Transaction $transaction)
    {
        parent::__construct($transaction);
    }
}
