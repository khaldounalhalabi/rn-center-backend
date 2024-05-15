<?php

namespace App\Services\Medicine;

use App\Models\Medicine;
use App\Services\Contracts\BaseService;
use App\Repositories\MedicineRepository;

/**
 * @implements IMedicineService<Medicine>
 * Class UserService
 */
class MedicineService extends BaseService implements IMedicineService
{
    /**
     * MedicineService constructor.
     * @param MedicineRepository $repository
     */
    public function __construct(MedicineRepository $repository)
    {
        parent::__construct($repository);
    }
}
