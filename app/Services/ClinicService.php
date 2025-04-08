<?php

namespace App\Services;

use App\Enums\ClinicStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    private ClinicSubscriptionService $clinicSubscriptionService;

    public function init(): void
    {
        parent::__construct();
        $this->userRepository = UserRepository::make();
        $this->scheduleService = ScheduleService::make();
        $this->clinicSubscriptionService = ClinicSubscriptionService::make();
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Clinic|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Clinic
    {
        try {
            DB::beginTransaction();
            if (!isset($data['user'])
                || !isset($data['speciality_ids'])
            ) {
                DB::commit();
                return null;
            }

            /** @var User $user */
            $user = $this->userRepository->create($data['user']);
            $user->assignRole(RolesPermissionEnum::DOCTOR['role']);
            $data['user_id'] = $user->id;

            /** @var Clinic $clinic */
            $clinic = $this->repository->create($data);

            $clinic->specialities()->sync($data['speciality_ids']);

            $this->scheduleService->setDefaultClinicSchedule($clinic);

            $this->clinicSubscriptionService->store([
                'clinic_id' => $clinic->id,
                'subscription_id' => $data['subscription_id'],
                'type' => $data['subscription_type'],
                'deduction_cost' => $data['subscription_deduction_cost'] ?? 0,
            ]);
            DB::commit();
            return $clinic->refresh()
                ->load($relationships)
                ->loadCount($countable);
        } catch (Exception $exception) {
            logger()->info($exception->getMessage());
            DB::rollBack();
            return null;
        }
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
        try {
            DB::beginTransaction();
            $clinic = $this->repository->find($id);

            if (!$clinic) {
                return null;
            }

            if (!$clinic->canUpdate()) {
                DB::commit();
                return null;
            }

            $clinic = $this->repository->update($data, $clinic);


            $user = $clinic->user;

            if (isset($data['user'])) {
                if (isset($data['password']) && $data['password'] == "") {
                    unset($data['password']);
                }
                $this->userRepository->update($data['user'], $clinic->user_id);
            }

            if (isset($data['speciality_ids'])) {
                $clinic->specialities()->sync($data['speciality_ids']);
            }

            DB::commit();
            return $clinic->load($relationships)->loadCount($countable);
        } catch (Exception $exception) {
            logger()->info($exception->getMessage());
            DB::rollBack();
            return null;
        }
    }

    /**
     * @param $clinicId
     * @return array
     */
    public function getClinicAvailableTimes($clinicId): array
    {
        $clinic = $this->repository->find($clinicId, ['validAppointments', 'schedules', 'validHolidays']);

        if (!$clinic) {
            return [
                'booked_times' => [],
                'clinic_schedule' => [],
                'clinic_holidays' => [],
            ];
        }

        $bookedTimes = AppointmentRepository::make()->getByClinicDayRange($clinic)
            ->groupBy('date')
            ->map(function (Collection $appointments) {
                return $appointments->count();
            });

        $schedules = $clinic->schedules->groupBy('day_of_week');
        $holidays = $clinic->validHolidays;

        return [
            'booked_times' => $bookedTimes,
            'clinic_schedule' => $schedules,
            'clinic_holidays' => $holidays,
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

    /**
     * @param       $subscriptionId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return array|null
     */
    public function getBySubscription($subscriptionId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->byActiveSubscription($subscriptionId, $relations, $countable);
    }

    public function getBySystemOffer($systemOfferId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getBySystemOffer($systemOfferId, $relations, $countable);
    }

    public function getOnlineBySpecialityId($specialityId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getOnlineClinicsBySpeciality($specialityId, $relations, $countable);
    }
}
