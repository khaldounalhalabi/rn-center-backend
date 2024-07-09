<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Setting>
 */
class SettingRepository extends BaseRepository
{
    protected string $modelClass = Setting::class;
}
