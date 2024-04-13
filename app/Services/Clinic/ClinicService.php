<?php

namespace App\Services\Clinic;

use App\Models\Clinic;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\PhoneNumberRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IClinicService<Clinic>
 * Class UserService
 */
class ClinicService extends BaseService implements IClinicService
{
    private UserRepository $userRepository;
    private AddressRepository $addressRepository;
    private PhoneNumberRepository $phoneNumberRepository;

    /**
     * ClinicService constructor.
     *
     * @param ClinicRepository $repository
     * @param UserRepository $userRepository
     * @param AddressRepository $addressRepository
     * @param PhoneNumberRepository $phoneNumberRepository
     */
    public function __construct(ClinicRepository $repository, UserRepository $userRepository, AddressRepository $addressRepository, PhoneNumberRepository $phoneNumberRepository)
    {
        parent::__construct($repository);
        $this->userRepository = $userRepository;
        $this->addressRepository = $addressRepository;
        $this->phoneNumberRepository = $phoneNumberRepository;
    }

    /**
     * @param array $data
     * @param array $relationships
     * @return Clinic|null
     */
    public function store(array $data, array $relationships = []): ?Clinic
    {
        if (!isset($data['user'])
            || !isset($data['address'])
            || !isset($data['speciality_ids'])
            || !isset($data['phone_numbers'])
        ) {
            return null;
        }

        /** @var User $user */
        $user = $this->userRepository->create($data['user']);
        $data['user_id'] = $user->id;

        /** @var Clinic $clinic */
        $clinic = $this->repository->create($data);

        $clinic->specialities()->sync($data['speciality_ids']);

        $data['address']['addressable_id'] = $user->id;
        $data['address']['addressable_type'] = User::class;

        $this->addressRepository->create($data['address']);

        $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);

        return $clinic->load($relationships);
    }

    /**
     * @param array $data
     * @param $id
     * @param array $relationships
     * @return Clinic|null
     */
    public function update(array $data, $id, array $relationships = []): ?Clinic
    {
        /** @var Clinic $clinic */
        $clinic = $this->repository->update($data, $id);

        if (!$clinic) return null;

        $user = $clinic->user;

        if (isset($data['user'])) {
            if (isset($data['password']) && $data['password'] == "") {
                unset($data['password']);
            }
            $this->userRepository->update($data['user'], $clinic->user_id);
        }

        if (isset($data['address'])) {
            $user->address()->update($data['address']);
        }

        if (isset($data['speciality_ids'])) {
            $clinic->specialities()->sync($data['speciality_ids']);
        }

        return $clinic->load($relationships);
    }
}
