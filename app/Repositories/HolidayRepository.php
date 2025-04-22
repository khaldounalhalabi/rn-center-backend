<?php

namespace App\Repositories;

use App\Models\Holiday;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;

/**
 * @extends  BaseRepository<Holiday>
 */
class HolidayRepository extends BaseRepository
{
    protected string $modelClass = Holiday::class;

    public function isHoliday(string|Carbon $date): bool
    {
        $data = Carbon::parse($date);
        return $this->globalQuery()
            ->where('from', '<=', $data->format('Y-m-d'))
            ->where('to', '>=', $data->format('Y-m-d'))
            ->exists();
    }
}
