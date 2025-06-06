<?php

namespace App\Repositories;

use App\Enums\PayslipStatusEnum;
use App\Excel\BaseExporter;
use App\Models\Payrun;
use App\Models\Payslip;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @extends  BaseRepository<Payslip>
 */
class PayslipRepository extends BaseRepository
{
    protected string $modelClass = Payslip::class;

    public function changeStatusByPayrunWhereNotExcluded(int $payrunId, string $status): void
    {
        $this->globalQuery()
            ->where('payrun_id', $payrunId)
            ->where('status', '!=', PayslipStatusEnum::EXCLUDED->value)
            ->chunk(10, function (Collection $payslips) use ($status) {
                $payslips->each(function (Payslip $payslip) use ($status) {
                    $payslip->update([
                        'status' => $status
                    ]);
                });
            });
    }

    public function export(array $ids = null): BinaryFileResponse
    {
        $collection = $this->globalQuery(['user', 'formula'])
            ->withSum('benefits', 'amount')
            ->withSum('deductions', 'amount')
            ->whereIn('id', $ids)
            ->get();

        $requestedColumns = request("columns") ?? null;
        return Excel::download(
            new BaseExporter($collection, $this->model, $requestedColumns),
            $this->model->getTable() . ".xlsx",
        );
    }

    public function bulkToggleStatus(array $data): void
    {
        /** @var Payrun $payrun */
        $payrun = null;
        $this->globalQuery()
            ->whereIn('id', $data['ids'])
            ->with('payrun')
            ->chunk(10, function (Collection $payslips) use ($data, &$payrun) {
                $payslips->each(function (Payslip $payslip) use ($data, &$payrun) {
                    if ($payslip->status != $data['status'] && $payslip->canUpdate()) {
                        $payslip->update([
                            'status' => $data['status']
                        ]);
                    }
                    $payrun = $payslip->payrun;
                });
            });

        $payrun?->calculatePaymentCost();
    }

    public function getByPayrun($payrunId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->withSum('benefits', 'amount')
                ->withSum('deductions', 'amount')
                ->where('payrun_id', $payrunId)
        );
    }

    public function getByIdsAndPayrun($payrunId, array $ids = [], array $relations = [], array $countable = []): array|LazyCollection
    {
        return $this->globalQuery($relations, $countable)
            ->whereIn('id', $ids)
            ->where('payrun_id', $payrunId)
            ->cursor();
    }

    public function getAllByPayrun($payrunId, array $relations = [], array $countable = []): array|LazyCollection
    {
        return $this->globalQuery($relations, $countable)
            ->where('payrun_id', $payrunId)
            ->cursor();
    }

    public function getByFormulaAndPayrun($payrunId, array $formulaIds = [], array $relations = [], array $countable = []): array|LazyCollection
    {
        return $this->globalQuery($relations, $countable)
            ->whereIn('formula_id', $formulaIds)
            ->where('payrun_id', $payrunId)
            ->cursor();
    }

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('user_id', $userId)
        );
    }
}
