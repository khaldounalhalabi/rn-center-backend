<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Setting>
 * @property SettingRepository $repository
 */
class SettingService extends BaseService
{
    use Makable;

    protected string $repositoryClass = SettingRepository::class;

    public function getByLabel(string $label , array $relations =[]): ?Setting
    {
        return $this->repository->getByLabel($label , $relations);
    }
}
