<?php

namespace App\Repositories;

use App\Enums\PermissionEnum;
use App\Enums\RolesPermissionEnum;
use App\Excel\Exporters\AttendanceLogExampleExport;
use App\Excel\Exporters\AttendanceLogExport;
use App\Excel\Importers\AttendanceLogImport;
use App\Models\AttendanceLog;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<AttendanceLog>
 */
class AttendanceLogRepository extends BaseRepository
{
    protected string $modelClass = AttendanceLog::class;

    public function deleteByAttendanceAndUser($attendanceId, int $userId): bool
    {
        return $this->globalQuery()
            ->where('user_id', $userId)
            ->where('attendance_id', $attendanceId)
            ->delete();
    }

    public function insert(array $data): bool
    {
        return AttendanceLog::insert($data);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getImportExample(): BinaryFileResponse
    {
        $collection = UserRepository::make()->globalQuery()
            ->selectRaw('full_name, id')
            ->withWhereHas('roles', function (Role|Builder|MorphToMany $query) {
                $query->whereIn('name', [
                    RolesPermissionEnum::SECRETARY['role'],
                    RolesPermissionEnum::DOCTOR['role']
                ]);
            })
            ->get()
            ->map(fn(User $user) => [
                'full_name' => $user->full_name,
                'user_id' => $user->id,
                'attend_at' => now()->format('Y-m-d') . " 09:00",
                'role' => $user?->roles?->first()?->name,
            ]);

        $duplicated = clone $collection;
        $duplicated = $duplicated->map(function ($item) {
            $item['attend_at'] = now()->format('Y-m-d') . " 17:00";
            return $item;
        });
        $collection->push(...$duplicated);
        return Excel::download(
            new AttendanceLogExampleExport($collection->sortBy('user_id'), $this->model, null, true),
            $this->model->getTable() . '-example.xlsx'
        );
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(array $ids = null): BinaryFileResponse
    {
        $collection = $this->globalQuery([], [], false)
            ->join('users', 'attendance_logs.user_id', '=', 'users.id')
            ->with('user.roles')
            ->orderByRaw('users.id ASC , attendance_logs.attend_at ASC')
            ->get()
            ->map(fn(AttendanceLog $attendance) => [
                'full_name' => $attendance->user->full_name,
                'user_id' => $attendance->user_id,
                'role' => $attendance?->user?->roles?->first()?->name,
                'attend_at' => $attendance->attend_at?->format('Y-m-d H:i'),
                'type' => $attendance->type,
            ]);

        return Excel::download(
            new AttendanceLogExport($collection, $this->model),
            $this->model->getTable() . ".xlsx",
        );
    }

    public function exportByUser(int $userId): BinaryFileResponse
    {
        $collection = $this->globalQuery(['user.roles'], [], false)
            ->where('attendance_logs.user_id', $userId)
            ->join('users', 'attendance_logs.user_id', '=', 'users.id')
            ->orderByRaw('users.id ASC , attendance_logs.attend_at ASC')
            ->get()
            ->map(fn(AttendanceLog $attendance) => [
                'full_name' => $attendance->user->full_name,
                'user_id' => $attendance->user_id,
                'role' => $attendance?->user?->roles?->first()?->name,
                'attend_at' => $attendance->attend_at?->format('Y-m-d H:i'),
                'type' => $attendance->type,
            ]);

        return Excel::download(
            new AttendanceLogExport($collection, $this->model),
            $this->model->getTable() . ".xlsx",
        );
    }

    public function import(): void
    {
        try {
            Excel::import(new AttendanceLogImport($this->model), request()->file('excel_file'));
        } catch (Exception|Error $error) {
            if (app()->environment('local')) {
                throw $error;
            }
            throw ValidationException::withMessages([
                'file_url' => [
                    'message' => __('site.invalid_excel_data'),
                ],
            ]);
        }
    }

    /**
     * @param               $userId
     * @param Carbon|string $fromDate
     * @param Carbon|string $toDate
     * @param array         $relations
     * @param array         $countable
     * @return Collection<AttendanceLog>|EloquentCollection<AttendanceLog>
     */
    public function getInRange($userId, Carbon|string $fromDate, Carbon|string $toDate, array $relations = [], array $countable = []): Collection|EloquentCollection
    {
        return $this->globalQuery($relations, $countable, false)
            ->where('user_id', $userId)
            ->where('attend_at', '>=', Carbon::parse($fromDate)->startOfDay()->format('Y-m-d H:i:s'))
            ->where('attend_at', '<=', Carbon::parse($toDate)->endOfDay()->format('Y-m-d H:i:s'))
            ->get();
    }

    public function getInRangeAttendanceCount($userId, Carbon|string $fromDate, Carbon|string $toDate): int
    {
        return $this->globalQuery(defaultOrder: false)
            ->selectRaw("DATE(attend_at)")
            ->where('user_id', $userId)
            ->where('attend_at', '>=', Carbon::parse($fromDate)->startOfDay()->format('Y-m-d H:i:s'))
            ->where('attend_at', '<=', Carbon::parse($toDate)->endOfDay()->format('Y-m-d H:i:s'))
            ->groupByRaw('user_id, DATE(attend_at)')
            ->get()
            ->count();
    }

    public function deleteByDateAndUser(int $userId, Carbon|string $date): bool
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $this->globalQuery()
            ->where('user_id', $userId)
            ->whereDate('attend_at', $date)
            ->delete();
    }

    /**
     * @param int    $userId
     * @param string $year
     * @param string $month
     * @return EloquentCollection<AttendanceLog>
     */
    public function getByUserAndYearAndMonth(int $userId, string $year, string $month): EloquentCollection
    {
        return $this->globalQuery(defaultOrder: false)
            ->select(['attendance_logs.status', 'attendance_logs.attend_at', 'attendance_logs.attendance_id', 'attendances.date'])
            ->join('attendances', 'attendances.id', 'attendance_logs.attendance_id')
            ->where('user_id', $userId)
            ->whereYear('attend_at', $year)
            ->whereMonth('attend_at', $month)
            ->orderByDesc('attend_at')
            ->get()
            ->groupBy('date');
    }

    public function getLatestLogInDay(string $date, int $userId, array $relations = [], array $countable = []): ?AttendanceLog
    {
        return $this->globalQuery($relations, $countable)
            ->where('user_id', $userId)
            ->whereDate('attend_at', $date)
            ->orderByDesc('attend_at')
            ->first();
    }
}
