<?php

namespace App\Services\Hospital;

use App\Models\Hospital;
use App\Services\Contracts\IBaseService;

/**
 * @extends IBaseService<Hospital>
 * Interface IUserService
 */
interface IHospitalService extends IBaseService
{
    /**
     * @param $hospitalId
     * @return Hospital|null
     */
    public function toggleHospitalStatus($hospitalId): ?Hospital;
}
