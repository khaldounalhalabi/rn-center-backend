<?php

namespace App\Services\Hospital;

use App\Models\Hospital;
use App\Repositories\AddressRepository;
use App\Repositories\PhoneNumberRepository;
use App\Services\Contracts\BaseService;
use App\Repositories\HospitalRepository;

/**
 * @implements IHospitalService<Hospital>
 * Class UserService
 */
class HospitalService extends BaseService implements IHospitalService
{
    private PhoneNumberRepository $phoneNumberRepository;
    private AddressRepository $addressRepository;

    /**
     * HospitalService constructor.
     *
     * @param HospitalRepository $repository
     * @param PhoneNumberRepository $phoneNumberRepository
     * @param AddressRepository $addressRepository
     */
    public function __construct(HospitalRepository $repository, PhoneNumberRepository $phoneNumberRepository, AddressRepository $addressRepository)
    {
        parent::__construct($repository);
        $this->phoneNumberRepository = $phoneNumberRepository;
        $this->addressRepository = $addressRepository;
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Hospital
    {
        /** @var Hospital $hospital */
        $hospital = parent::store($data);
        if ($data['available_departments']) {
            $hospital->availableDepartments()->sync($data['available_departments']);
        }

        if ($data['phone_numbers']) {
            $this->phoneNumberRepository->insert($data['phone_numbers'], Hospital::class, $hospital->id);
        }

        if (isset($data['address'])) {
            $data['address']['addressable_id'] = $hospital->id;
            $data['address']['addressable_type'] = Hospital::class;
            $this->addressRepository->create($data['address']);
        }

        return $hospital->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Hospital
    {
        /** @var Hospital $hospital */
        $hospital = parent::update($data, $id, $relationships, $countable);
        if (isset($data['available_departments'])) {
            $hospital->availableDepartments()->sync($data['available_departments']);
        }

        if (isset($data['phone_numbers'])) {
            $hospital->phones()->delete();
            $this->phoneNumberRepository->insert($data['phone_numbers'], Hospital::class, $hospital->id);
        }

        if (isset($data['address'])) {
            $hospital->address()->updateOrCreate($data['address']);
        }

        return $hospital;
    }
}
