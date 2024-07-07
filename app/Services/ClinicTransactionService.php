<?php

namespace App\Services;

use App\Enums\ClinicTransactionTypeEnum;
use App\Models\ClinicTransaction;
use App\Repositories\ClinicTransactionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<ClinicTransaction>
 * @property ClinicTransactionRepository $repository
 */
class ClinicTransactionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicTransactionRepository::class;

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var ClinicTransaction $transaction */
        $transaction = parent::view($id, $relationships, $countable);
        if (!$transaction?->canShow()) {
            return null;
        }

        return $transaction;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $transaction = $this->repository->find($id);

        if (!$transaction?->canUpdate()) {
            return null;
        }

        return $this->repository->update($data, $transaction, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $transaction = $this->repository->find($id);

        if (!$transaction?->canDelete()) {
            return false;
        }

        $transaction->delete();
        return true;
    }

    public function summary(): array
    {
        if (!auth()->user()?->isClinic()) {
            return [];
        }

        $data['clinic_balance'] = auth()->user()?->clinic?->balance?->balance ?? 0;
        $data['pending_amount'] = $this->repository->getPendingTransactions()->sum(function (ClinicTransaction $clinicTransaction) {
            if (in_array($clinicTransaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
                return -($clinicTransaction->amount);
            } else {
                return $clinicTransaction->amount;
            }
        });

        return $data;
    }
}
