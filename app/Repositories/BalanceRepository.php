<?php

namespace App\Repositories;

use App\Models\Balance;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Balance>
 */
class BalanceRepository extends BaseRepository
{
    protected string $modelClass = Balance::class;
}
