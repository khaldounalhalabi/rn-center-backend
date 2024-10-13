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

    /**
     * @param string $label
     * @return Setting|null
     */
    public function getByLabel(string $label , array $relations =[]): ?Setting
    {
        return $this->globalQuery($relations)->where('label', $label)->first();
    }
}
