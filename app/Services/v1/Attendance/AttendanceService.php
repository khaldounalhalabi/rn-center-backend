<?php

namespace App\Services\v1\Attendance;

use App\Enums\AttendanceStatusEnum;
use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;

/**
 * @extends BaseService<Attendance>
 * @property AttendanceRepository $repository
 */
class AttendanceService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AttendanceRepository::class;


    /**
     * @return Attendance|null
     */
    public function markAsApproved(): ?Attendance
    {
        $attendance = $this->repository->getByDateOrCreate(Carbon::parse(request('attendance_at', now())));
        return $this->repository->update([
            'status' => AttendanceStatusEnum::APPROVED->value,
        ], $attendance);
    }

    public function getPending(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getPending($relations, $countable);
    }
}
