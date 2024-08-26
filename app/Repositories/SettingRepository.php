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
    public function getByLabel(string $label): ?Setting
    {
        return $this->globalQuery()->where('label', $label)->first();
    }
}
