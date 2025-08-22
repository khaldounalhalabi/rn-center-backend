<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Models\Balance;
use App\Services\BalanceService;

class BalanceController extends ApiController
{
    private BalanceService $balanceService;

    public function balanceTrend()
    {
        return Balance::where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth())
            ->select(['balance' , 'created_at'])
            ->get();
    }
}
