<?php

namespace App\Services;

use App\Models\Medicine;
use App\Repositories\MedicineRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Medicine>
 * @property MedicineRepository $repository
 */
class MedicineService extends BaseService
{
    use Makable;

    protected string $repositoryClass = MedicineRepository::class;
}
