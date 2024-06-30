<?php

namespace App\Services;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\AppointmentDeduction;
use App\Repositories\AppointmentDeductionRepository;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<AppointmentDeduction>
 * @property AppointmentDeductionRepository $repository
 */
class AppointmentDeductionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentDeductionRepository::class;

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        $deduction = $this->repository->find($id, $relationships, $countable);
        if (!$deduction?->canShow()) {
            return null;
        }

        return $deduction;
    }

    public function toggleStatus($appointmentDeductionId)
    {
        $deduction = $this->repository->find($appointmentDeductionId);

        if (!$deduction) {
            return null;
        }

        if ($deduction->status == AppointmentDeductionStatusEnum::PENDING->value) {
            $adminTransaction = TransactionRepository::make()->create([
                'amount'      => abs($deduction->amount),
                'date'        => now(),
                'type'        => $deduction->amount > 0 ? TransactionTypeEnum::INCOME->value : TransactionTypeEnum::OUTCOME->value,
                'actor_id'    => auth()->user()?->id,
                'description' => "An appointment deduction for the appointment with id : $deduction->appointment_id in {$deduction->clinic?->name}",
            ]);
            $deduction->update([
                'status'         => AppointmentDeductionStatusEnum::DONE->value,
                'transaction_id' => $adminTransaction->id,
            ]);
            $deduction->clinicTransaction->update([
                'status' => ClinicTransactionStatusEnum::DONE->value,
            ]);
        } elseif ($deduction->status == AppointmentDeductionStatusEnum::DONE->value) {
            TransactionRepository::make()->delete($deduction->transaction_id);
            $deduction->update([
                'status'         => AppointmentDeductionStatusEnum::PENDING->value,
                'transaction_id' => null
            ]);
            $deduction->clinicTransaction->update([
                'status' => ClinicTransactionStatusEnum::PENDING->value,
            ]);
        }

        return $deduction->status;
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }
}
