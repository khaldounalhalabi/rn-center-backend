<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Services\BalanceService;

class BalanceController extends ApiController
{
    private BalanceService $balanceService;
}
