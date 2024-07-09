<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends SettingService<Setting>
 * @property SettingRepository $repository
 */
class SettingService extends BaseService
{
    use Makable;

    protected string $repositoryClass = SettingRepository::class;
}
