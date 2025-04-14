<?php

namespace App\Services;

use App\Enums\ClinicStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Support\Collection;
use Throwable;

/**
 * @extends BaseService<Clinic>
 * @property ClinicRepository $repository
 */
class ClinicService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicRepository::class;

    private UserRepository $userRepository;
    private ScheduleService $scheduleService;

    public function init(): void
    {
        parent::__construct();
        $this->userRepository = UserRepository::make();
        $this->scheduleService = ScheduleService::make();
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Clinic|null
     * @throws Throwable
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Clinic
    {
        $user = $this->userRepository->create($data['user']);
        $user->assignRole(RolesPermissionEnum::DOCTOR['role']);
        $data['user_id'] = $user->id;

        /** @var Clinic $clinic */
        $clinic = $this->repository->create($data);

        $clinic->specialities()->sync($data['speciality_ids']);

        $this->scheduleService->setDefaultClinicSchedule($clinic);

        return $clinic->refresh()
            ->load($relationships)
            ->loadCount($countable);
    }

    /**
     * @param array $data
     * @param       $id
     * @param array $relationships
     * @param array $countable
     * @return Clinic|null
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Clinic
    {
        $clinic = $this->repository->find($id);

        if (!$clinic) {
            return null;
        }

        $clinic = $this->repository->update($data, $clinic);

        $this->userRepository->update($data['user'], $clinic->user_id);

        if (isset($data['speciality_ids'])) {
            $clinic->specialities()->sync($data['speciality_ids']);
        }

        return $clinic->load($relationships)->loadCount($countable);
    }

    /**
     * @param $clinicId
     * @return array
     */
    public function getClinicAvailableTimes($clinicId): array
    {
        $clinic = $this->repository->find($clinicId, ['validAppointments', 'schedules']);

        if (!$clinic) {
            return [
                'booked_times' => [],
                'clinic_schedule' => [],
            ];
        }

        $bookedTimes = AppointmentRepository::make()->getByClinicDayRange($clinic)
            ->groupBy('date')
            ->map(function (Collection $appointments) {
                return $appointments->count();
            });

        $schedules = $clinic->schedules->groupBy('day_of_week');

        return [
            'booked_times' => $bookedTimes,
            'clinic_schedule' => $schedules,
        ];
    }

    /**
     * @param $clinicId
     * @return string|null
     */
    public function toggleClinicStatus($clinicId): ?string
    {
        $clinic = $this->repository->find($clinicId);

        if (!$clinic) {
            return null;
        }

        if ($clinic->status == ClinicStatusEnum::ACTIVE->value) {
            $clinic->status = ClinicStatusEnum::INACTIVE->value;
        } else {
            $clinic->status = ClinicStatusEnum::ACTIVE->value;
        }

        $clinic->save();

        return $clinic->status;
    }

    public function getOnlineBySpecialityId($specialityId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getOnlineClinicsBySpeciality($specialityId, $relations, $countable);
    }
}
