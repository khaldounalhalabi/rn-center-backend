<?php

namespace App\Services\Customer;

use App\Enums\RolesPermissionEnum;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Services\Contracts\BaseService;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ICustomerService
 * @extends BaseService<Customer>
 */
class CustomerService extends BaseService implements ICustomerService
{

    private UserService $userService;

    /**
     * CustomerService constructor.
     * @param CustomerRepository $repository
     * @param UserService        $userService
     */
    public function __construct(CustomerRepository $repository, UserService $userService)
    {
        parent::__construct($repository);
        $this->userService = $userService;
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $user = $this->userService->store($data);
        $user->assignRole(RolesPermissionEnum::CUSTOMER['role']);
        return $this->repository->create([
            'user_id' => $user->id
        ], $relationships);
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
}
