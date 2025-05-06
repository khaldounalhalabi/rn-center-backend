<?php

namespace App\Repositories;

use App\Models\PayslipAdjustment;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<PayslipAdjustment>
 */
class PayslipAdjustmentRepository extends BaseRepository
{
    protected string $modelClass = PayslipAdjustment::class;

    public function insert(array $data): bool
    {
        return PayslipAdjustment::insert($data);
    }
}
