<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\PatientProfileRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Customer>
 * @property CustomerRepository $repository
 */
class CustomerService extends BaseService
{
    use Makable;

    protected string $repositoryClass = CustomerRepository::class;

    private UserService $userService;


    public function init(): void
    {
        parent::__construct();
        $this->userService = UserService::make();
    }

    /**
     * @param array{first_name:string,last_name:string,email:string,birth_date:string,gender:string,name:string,medical_condition:string,note:string,other_data:string,images:string, $data
     * @param array                                                                                                                                                                                  $relations
     * @param array                                                                                                                                                                                  $countable
     * @return Customer|null
     * @throws RoleDoesNotExistException
     */
    public function doctorAddCustomer(array $data = [], array $relations = [], array $countable = []): ?Customer
    {
        $user = UserRepository::make()->getExistCustomerUser([
            'email' => $data['email'] ?? null,
            'phone_numbers' => $data['phone_numbers'] ?? null,
        ]);

        if (!$user) {
            $data['role'] = RolesPermissionEnum::CUSTOMER['role'];
            $user = $this->userService->store($data);
            $customer = $this->repository->create([
                'user_id' => $user->id,
            ]);
        } else {
            $customer = $this->repository->getByUserId($user->id);
        }

        if (!$customer) {
            $customer = $this->repository->create([
                'user_id' => $user->id,
            ]);
        }

        return $this->createUpdateClinicPatientProfile($customer, $data, $relations, $countable);
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $user = $this->userService->store($data);
        $user->assignRole(RolesPermissionEnum::CUSTOMER['role']);
        return $this->repository->create([
            'user_id' => $user->id,
        ], $relationships);
    }

    /**
     * @param Model|Customer|null $customer
     * @param array               $data
     * @param array               $relations
     * @param array               $countable
     * @return Customer|Model|null
     */
    private function createUpdateClinicPatientProfile(Model|Customer|null $customer, array $data, array $relations, array $countable): null|Customer|Model
    {
        $patientProfile = PatientProfileRepository::make()->getByClinicAndCustomer(auth()->user()?->getClinicId(), $customer->id);

        if ($patientProfile) {
            if ($patientProfile->canUpdate()) {
                PatientProfileRepository::make()->update($data, $patientProfile);
            } else {
                return null;
            }
        } else {
            PatientProfileRepository::make()->create([
                'customer_id' => $customer->id,
                'clinic_id' => auth()->user()?->getClinicId(),
                ...$data,
            ]);
        }

        return $customer->load($relations)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $customer = $this->repository->find($id);
        if (!$customer) {
            return null;
        }

        $this->userService->update($data, $customer->user_id);
        $customer->refresh();

        return $customer->load($relationships)
            ->loadCount($countable);
    }

    public function doctorUpdateCustomer(int $customerId, array $data, array $relations = [], array $countable = []): Customer|null
    {
        $customer = $this->repository->find($customerId);

        if (!$customer?->canUpdate()) {
            return null;
        }
        return $this->createUpdateClinicPatientProfile($customer, $data, $relations, $countable);
    }

    public function doctorDeleteCustomer($customerId): ?bool
    {
        $customer = $this->repository->find($customerId);
        if (!$customer) {
            return null;
        }

        $patientProfile = PatientProfileRepository::make()->getByClinicAndCustomer(auth()->user()?->getClinicId(), $customer->id);

        if ($patientProfile) {
            $patientProfile->delete();
            return true;
        }

        return null;
    }

    public function delete($id): ?bool
    {
        $customer = $this->repository->find($id, ['user']);

        if (!$customer) {
            return null;
        }

        $user = $customer->user;
        $customer->delete();
        return $user->delete();
    }

    public function getDoctorCustomers(array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getClinicCustomers(auth()?->user()?->getClinicId() ?? 0, $relations, $countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        $customer = parent::view($id, $relationships, $countable);
        if ($customer?->canShow()) {
            return $customer;
        }

        return null;
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getClinicCustomers($clinicId, $relations, $countable);
    }

    public function getRecent(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getRecent($relations, $countable);
    }
}
