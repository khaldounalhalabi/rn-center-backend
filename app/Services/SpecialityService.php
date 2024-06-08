<?php

namespace App\Services;

use App\Models\Speciality;
use App\Repositories\SpecialityRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Speciality>
 * @property SpecialityRepository $repository
 */
class SpecialityService extends BaseService
{
    use Makable;

    protected string $repositoryClass = SpecialityRepository::class;
}
