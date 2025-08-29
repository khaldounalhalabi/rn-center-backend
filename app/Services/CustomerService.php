<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
use App\Enums\PermissionEnum;
use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\Customer;
use App\Modules\PDF;
use App\Modules\SMS;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * @extends BaseService<Customer>
 * @property CustomerRepository $repository
 */
class CustomerService extends BaseService
{
    use Makable;

    protected string $repositoryClass = CustomerRepository::class;

    public function init(): void
    {
        parent::__construct();
    }

    /**
     * @throws RoleDoesNotExistException
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $data['password'] = Str::password('10');
        $user = UserRepository::make()->create($data);
        $user->assignRole(RolesPermissionEnum::CUSTOMER['role']);
        $customer = $this->repository->create([
            'user_id' => $user->id,
            ...$data
        ], $relationships, $countable);

        SMS::make()
            ->message(trans('site.new_patient_account_created', [
                'patient_name' => $user->full_name,
                'password' => $data['password'],
                'app_link' => config('settings.app_url')
            ]))->to($user->universal_phone)
            ->send();

        return $customer;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $customer = $this->repository->find($id);
        if (!$customer) {
            return null;
        }

        UserRepository::make()->update($data, $customer->user_id);
        return $this->repository->update($data, $customer, $relationships, $countable);
    }

    public function getRecent(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getRecent($relations, $countable);
    }

    /**
     * @throws Throwable
     */
    public function toPdf(int $customerId): ?string
    {
        $customer = $this->repository->find($customerId, [
            'appointments',
            'appointments.clinic.user',
            'appointments.service',
            'medicalRecords',
            'medicalRecords.clinic.user',
            'prescriptions',
            'prescriptions.clinic.user',
            'prescriptions.medicinePrescriptions',
            'prescriptions.medicinePrescriptions.medicine',
            'user',
        ]);

        if (!$customer) {
            return null;
        }

        $appointments = $customer
            ->appointments
            ->where('status', '=', AppointmentStatusEnum::CHECKOUT->value)
            ->sortByDesc('date_time');
        $prescriptions = $customer->prescriptions->sortByDesc('created_at');
        $medicalRecords = $customer->medicalRecords->sortByDesc('created_at');

        return PDF::viewToPdf(view('pdf.patient-report', [
            'customer' => $customer,
            'appointments' =>
                isAdmin()
                || isDoctor()
                || (isSecretary() && can(PermissionEnum::APPOINTMENT_MANAGEMENT))
                || (isCustomer() && customer()->id == $customerId)
                    ? $appointments
                    : null,
            'prescriptions' =>
                isAdmin()
                || isDoctor()
                || (isSecretary() && can(PermissionEnum::APPOINTMENT_MANAGEMENT))
                || (isCustomer() && customer()->id == $customerId)
                    ? $prescriptions
                    : null,
            'medicalRecords' => $medicalRecords,
        ]));
    }
}
