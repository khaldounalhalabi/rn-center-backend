<?php

namespace App\Services\Hospital;

use App\Models\Hospital;
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

    /**
     * HospitalService constructor.
     *
     * @param HospitalRepository $repository
     * @param PhoneNumberRepository $phoneNumberRepository
     */
    public function __construct(HospitalRepository $repository, PhoneNumberRepository $phoneNumberRepository)
    {
        parent::__construct($repository);
        $this->phoneNumberRepository = $phoneNumberRepository;
    }

    public function store(array $data, array $relationships = []): ?Hospital
    {
        /** @var Hospital $hospital */
        $hospital = parent::store($data, $relationships);
        if ($data['available_departments']) {
            $hospital->availableDepartments()->sync($data['available_departments']);
        }

        if ($data['phone_numbers']) {
            $this->phoneNumberRepository->insert($data['phone_numbers'], Hospital::class, $hospital->id);
        }

        return $hospital->load($relationships);
    }

    public function update(array $data, $id, array $relationships = []): ?Hospital
    {
        /** @var Hospital $hospital */
        $hospital = parent::update($data, $id, $relationships);
        if (isset($data['available_departments'])) {
            $hospital->availableDepartments()->sync($data['available_departments']);
        }

        if (isset($data['phone_numbers'])) {
            $this->phoneNumberRepository->insert($data['phone_numbers'], Hospital::class, $hospital->id);
        }

        return $hospital->load($relationships);
    }
}
