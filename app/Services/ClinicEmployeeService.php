<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\ClinicEmployee;
use App\Notifications\RealTime\PermissionsChangeNotification;
use App\Repositories\ClinicEmployeeRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<ClinicEmployee>
 * @property ClinicEmployeeRepository $repository
 */
class ClinicEmployeeService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicEmployeeRepository::class;

    /**
     * @throws RoleDoesNotExistException
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?ClinicEmployee
    {
        $data['role'] = RolesPermissionEnum::CLINIC_EMPLOYEE['role'];
        $user = UserService::make()->store($data);

        foreach (RolesPermissionEnum::CLINIC_EMPLOYEE['permissions'] as $permission => $model) {
            $user->assignPermission($permission, $model);
        }

        return $this->repository->create([
            'clinic_id' => $data['clinic_id'],
            'user_id' => $user->id
        ], $relationships, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $clinicEmployee = $this->repository->find($id);

        if (!$clinicEmployee?->canUpdate()) {
            return null;
        }

        UserService::make()->update($data, $clinicEmployee->user_id);
        return $clinicEmployee->load($relationships)->loadCount($countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var ClinicEmployee $clinicEmployee */
        $clinicEmployee = parent::view($id, $relationships, $countable);
        if (!$clinicEmployee?->canShow()) {
            return null;
        }

        return $clinicEmployee;
    }

    public function delete($id): ?bool
    {
        $clinicEmployee = $this->repository->find($id);
        if (!$clinicEmployee?->canDelete()) {
            return null;
        }
        $clinicEmployee->user->delete();
        return true;
    }

    /**
     * @param       $clinicEmployeeId
     * @param array $data
     * @param array $relations
     * @param array $countable
     * @return ClinicEmployee|null
     */
    public function updateEmployeePermissions($clinicEmployeeId, array $data, array $relations = [], array $countable = []): ?ClinicEmployee
    {
        try {
            $clinicEmployee = $this->repository->find($clinicEmployeeId);

            if (!$clinicEmployee?->canUpdate()) {
                return null;
            }

            $user = $clinicEmployee->user;
            $user->removeAllPermissions();
            foreach (RolesPermissionEnum::CLINIC_EMPLOYEE['permissions'] as $permission => $model) {
                if (in_array($permission, $data['permissions'])) {
                    $user->assignPermission($permission, $model);
                }
            }
            FirebaseServices::make()
                ->setData([])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($user)
                ->setNotification(PermissionsChangeNotification::class)
                ->send();

            return $clinicEmployee->load($relations)->loadCount($countable);
        } catch (Exception) {
            return null;
        }
    }
}
