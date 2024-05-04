<?php

namespace App\Services\Clinic;

use App\Enums\ClinicStatusEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\PhoneNumberRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Services\Schedule\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @implements IClinicService<Clinic>
 * @extends BaseService<Clinic>
 * Class UserService
 */
class ClinicService extends BaseService implements IClinicService
{
    private UserRepository $userRepository;
    private AddressRepository $addressRepository;
    private PhoneNumberRepository $phoneNumberRepository;
    private ScheduleService $scheduleService;

    /**
     * ClinicService constructor.
     *
     * @param ClinicRepository $repository
     * @param UserRepository $userRepository
     * @param AddressRepository $addressRepository
     * @param PhoneNumberRepository $phoneNumberRepository
     */
    public function __construct(
        ClinicRepository      $repository,
        UserRepository        $userRepository,
        AddressRepository     $addressRepository,
        PhoneNumberRepository $phoneNumberRepository,
        ScheduleService       $scheduleService
    )
    {
        parent::__construct($repository);
        $this->userRepository = $userRepository;
        $this->addressRepository = $addressRepository;
        $this->phoneNumberRepository = $phoneNumberRepository;
        $this->scheduleService = $scheduleService;
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Clinic|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Clinic
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

        $this->scheduleService->setDefaultClinicSchedule($clinic);

        return $clinic->load($relationships)->loadCount($countable);
    }

    /**
     * @param array $data
     * @param $id
     * @param array $relationships
     * @param array $countable
     * @return Clinic|null
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Clinic
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

        if ($data['phone_numbers']) {
            $user->phones()->delete();
            $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);
        }

        return $clinic->load($relationships)->loadCount($countable);
    }

    /**
     * @param $clinicId
     * @return array
     */
    public function getClinicAvailableTimes($clinicId): array
    {
        $clinic = $this->repository->find($clinicId, ['validAppointments', 'schedules', 'validHolidays']);
        $bookedTimes = $clinic->validAppointments->groupBy('date')
            ->map(fn(Collection $appointments, $index) => [
                'date' => Carbon::parse($index)->format('Y-m-d'),
                'times' => $appointments->map(fn(Appointment $appointment) => [
                    'from' => $appointment->from->format('H:i'),
                    'to' => $appointment->to->format('H:i'),
                ]),
            ])->values();

        $schedules = $clinic->schedules->groupBy('day_of_week');
        $holidays = $clinic->validHolidays;

        return [
            'booked_times' => $bookedTimes,
            'clinic_schedule' => $schedules,
            'clinic_holidays' => $holidays
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
}
