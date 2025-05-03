<?php

namespace App\Repositories;

use App\Enums\AttendanceStatusEnum;
use App\Models\Attendance;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;

/**
 * @extends  BaseRepository<Attendance>
 */
class AttendanceRepository extends BaseRepository
{
    protected string $modelClass = Attendance::class;


    /**
     * @param string|Carbon $date
     * @param array         $relations
     * @return Attendance
     */
    public function getByDateOrCreate(string|Carbon $date, array $relations = []): Attendance
    {
        $date = Carbon::parse($date);
        $attendance = $this->globalQuery($relations)
            ->whereDate('date', $date->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            $attendance = $this->create([
                'date' => $date->format('Y-m-d'),
                'status' => AttendanceStatusEnum::DRAFT->value,
            ]);
        }

        return $attendance;
    }

    public function getPending(array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable, false)
                ->where('status', AttendanceStatusEnum::DRAFT->value)
                ->orderBy('date')
        );
    }
}
