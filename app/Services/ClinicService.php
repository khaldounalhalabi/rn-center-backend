<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Repositories\ClinicRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
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
        UserService::make()->sendVerificationCode($user);
        $data['user_id'] = $user->id;

        /** @var Clinic $clinic */
        $clinic = $this->repository->create($data);

        if (isset($data['speciality_ids'])){
            $clinic->specialities()->sync($data['speciality_ids']);
        }

        $this->scheduleService->setDefaultSchedule($clinic);

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

        if (isset($data['speciality_ids'])){
            $clinic->specialities()->sync($data['speciality_ids']);
        }

        return $clinic->load($relationships)->loadCount($countable);
    }
}
