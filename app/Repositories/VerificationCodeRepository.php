<?php

namespace App\Repositories;

use App\Models\VerificationCode;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<VerificationCode>
 */
class VerificationCodeRepository extends BaseRepository
{
    protected string $modelClass = VerificationCode::class;

    public function getActiveByCode(string $code): ?VerificationCode
    {
        return $this->globalQuery()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }
}
