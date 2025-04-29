<?php

namespace App\Services;

use App\Models\Balance;
use App\Repositories\BalanceRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Balance>
 * @property BalanceRepository $repository
 */
class BalanceService extends BaseService
{
    use Makable;

    protected string $repositoryClass = BalanceRepository::class;

    public function getBalance(): Balance
    {
        return $this->repository->getBalance() ?? Balance::make([
            'balance' => 0,
            'id' => 1,
        ]);
    }
}
