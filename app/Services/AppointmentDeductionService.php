<?php

namespace App\Services;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\AppointmentDeduction;
use App\Repositories\AppointmentDeductionRepository;
use App\Repositories\ClinicRepository;
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
                'amount' => abs($deduction->amount),
                'date' => now(),
                'type' => $deduction->amount > 0 ? TransactionTypeEnum::INCOME->value : TransactionTypeEnum::OUTCOME->value,
                'actor_id' => auth()->user()?->id,
                'description' => "An appointment deduction for the appointment with id : $deduction->appointment_id in {$deduction->clinic?->name}",
            ]);
            $deduction->update([
                'status' => AppointmentDeductionStatusEnum::DONE->value,
                'transaction_id' => $adminTransaction->id,
            ]);
            $deduction->clinicTransaction?->update([
                'status' => ClinicTransactionStatusEnum::DONE->value,
            ]);
        } elseif ($deduction->status == AppointmentDeductionStatusEnum::DONE->value) {
            TransactionRepository::make()->delete($deduction->transaction_id);
            $deduction->update([
                'status' => AppointmentDeductionStatusEnum::PENDING->value,
                'transaction_id' => null,
            ]);
            $deduction->clinicTransaction?->update([
                'status' => ClinicTransactionStatusEnum::PENDING->value,
            ]);
        }

        return $deduction->status;
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }

    /**
     * @param int|null $clinicId
     * @return array
     */
    public function clinicSummary(?int $clinicId = null): array
    {
        if (!auth()->user()?->isClinic() && !$clinicId) {
            return [];
        }

        if ($clinicId) {
            $clinic = ClinicRepository::make()->find($clinicId, ['activeSubscription', 'activeSubscription.subscription', 'balance']);
        } else {
            $clinic = auth()->user()?->clinic;
        }

        $deductions = $this->repository->getPendingDeductions();
        $activeSubscription = $clinic?->activeSubscription;
        $data['appointments_deductions'] = $deductions->sum('amount');
        $data['subscription_cost'] = $activeSubscription?->subscription?->cost;
        $data['total_cost'] = $data['appointments_deductions'] + $data['subscription_cost'];
        $data['subscription_start'] = $activeSubscription?->start_time?->format('Y-m-d H:i');
        $data['subscription_end'] = $activeSubscription?->end_time?->format('Y-m-d H:i');
        $data['clinic_balance'] = $clinic->balance?->balance ?? 0;

        return $data;
    }

    public function adminSummary(): array
    {
        $data['pending_appointment_deductions'] = $this->repository
            ->getPendingDeductions()
            ->sum('amount');
        $data['done_appointment_deductions'] = $this->repository
            ->getDoneDeductions()
            ->sum('amount');
        $data['balance'] = auth()->user()?->balance()?->balance ?? 0;

        return $data;
    }

    public function deductionsSummedByMonth()
    {
        return $this->repository->deductionsSummedByMonth();
    }

    public function collectForThisMonth($clinicId): void
    {
        $ids = $this->repository->getByDateRange($clinicId, now()->firstOfMonth(), now()->lastOfMonth())->pluck('id');
        $this->bulkToggleStatus([
            'status' => AppointmentDeductionStatusEnum::DONE->value,
            'ids' => $ids->toArray(),
        ]);
    }

    public function bulkToggleStatus(array $data): void
    {
        $status = $data['status'];
        $this->repository->bulk(function (AppointmentDeduction $deduction) use ($status, &$clinicIds) {
            if (
                $deduction->status == AppointmentDeductionStatusEnum::PENDING->value
                && $status == AppointmentDeductionStatusEnum::DONE->value
            ) {
                $adminTransaction = TransactionRepository::make()->create([
                    'amount' => abs($deduction->amount),
                    'date' => now(),
                    'type' => $deduction->amount > 0 ? TransactionTypeEnum::INCOME->value : TransactionTypeEnum::OUTCOME->value,
                    'actor_id' => auth()->user()?->id,
                    'description' => "An appointment deduction for the appointment with id : $deduction->appointment_id in {$deduction->clinic?->name}",
                ]);
                $deduction->update([
                    'status' => AppointmentDeductionStatusEnum::DONE->value,
                    'transaction_id' => $adminTransaction->id,
                ]);
                $deduction->clinicTransaction?->update([
                    'status' => ClinicTransactionStatusEnum::DONE->value,
                ]);
            } elseif (
                $deduction->status == AppointmentDeductionStatusEnum::DONE->value
                && $status == AppointmentDeductionStatusEnum::PENDING->value
            ) {
                TransactionRepository::make()->delete($deduction->transaction_id);
                $deduction->update([
                    'status' => AppointmentDeductionStatusEnum::PENDING->value,
                    'transaction_id' => null,
                ]);
                $deduction->clinicTransaction?->update([
                    'status' => ClinicTransactionStatusEnum::PENDING->value,
                ]);
            }
        }, $data['ids']);
    }

    public function getDeductionsTotalForCurrentMonth($clinicId)
    {
        return $this->repository
            ->getByDateRange($clinicId, now()->firstOfMonth(), now()->lastOfMonth())
            ->sum(fn(AppointmentDeduction $deduction) => $deduction->status == $deduction->isPending() ? $deduction->amount : 0);
    }
}
