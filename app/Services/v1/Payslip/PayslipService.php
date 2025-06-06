<?php

namespace App\Services\v1\Payslip;

use App\Enums\PayrunStatusEnum;
use App\Enums\PayslipAdjustmentTypeEnum;
use App\Enums\PayslipStatusEnum;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\EquationParser;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Result;
use App\Models\Formula;
use App\Models\FormulaSegment;
use App\Models\Payrun;
use App\Models\Payslip;
use App\Models\PayslipAdjustment;
use App\Models\User;
use App\Modules\PDF;
use App\Repositories\PayrunRepository;
use App\Repositories\PayslipAdjustmentRepository;
use App\Repositories\PayslipRepository;
use App\Services\Contracts\BaseService;
use App\Services\v1\Payrun\PayrunService;
use App\Traits\Makable;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * @extends BaseService<Payslip>
 * @property PayslipRepository $repository
 */
class PayslipService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PayslipRepository::class;

    /**
     * @param User    $user
     * @param Formula $formula
     * @param Payrun  $payrun
     * @return array{array{label:string , value:numeric} , numeric , numeric , array{label:string , value:numeric}}
     */
    public static function getPayslipSegmentsDetails(User $user, Formula $formula, Payrun $payrun): array
    {
        $earnings = [];
        $totalEarnings = 0;
        $deductions = [];
        $totalDeductions = 0;
        $formula->formulaSegments->each(
        /**
         * @throws Exception
         */ function (FormulaSegment $formulaSegment) use (
            $formula,
            $payrun,
            $user,
            &$deductions,
            &$totalDeductions,
            &$totalEarnings,
            &$earnings,
        ) {
            $label = $formulaSegment->name ?? $formulaSegment->getTitle();
            $errors = null;
            $evaluation = EquationParser::parse($formulaSegment->segment)?->resolve(
                $user->id,
                $formula,
                $payrun->from,
                $payrun?->to
            );
            if (is_null($evaluation)) {
                $evaluation = new Result();
                $errors = Expression::getParsingFlags();
            } elseif ($evaluation->hasErrors()) {
                $errors = $evaluation->getErrors();
            }
            if ($evaluation->getResult() >= 0) {
                $earnings[] = [
                    'label' => $label,
                    'value' => $evaluation->getResult(),
                    'errors' => $errors ? collect($errors)->map(fn(ParsingError $e) => $e->toArray())->unique('flag') : null,
                ];
                $totalEarnings += $evaluation->getResult();
            } else {
                $deductions[] = [
                    'label' => $label,
                    'value' => abs($evaluation->getResult()),
                    'errors' => $errors ? collect($errors)->map(fn(ParsingError $e) => $e->toArray())->unique('flag') : null
                ];
                $totalDeductions += abs($evaluation->getResult());
            }

            return true;
        });
        return [
            $earnings,
            $deductions,
            $totalEarnings,
            $totalDeductions,
        ];
    }


    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Payslip
    {
        $payslip = $this->repository->find($id);

        if (!$payslip) {
            return null;
        }

        if (!$payslip->canUpdate()) {
            return null;
        }

        $grossPay = 0;
        foreach ($data['details']['earnings'] ?? [] as $earning) {
            $grossPay += $earning['value'] ?? 0;
        }

        foreach ($data['details']['deductions'] ?? [] as $deduction) {
            $grossPay -= $deduction['value'] ?? 0;
        }

        $details = $payslip->details;

        $data['gross_pay'] = $grossPay;

        $netPay = $data['gross_pay'] ?? $payslip->gross_pay;
        $adjustments = [];

        if (isset($data['payslip_adjustments'])) {
            $payslip->payslipAdjustments()->delete();
            foreach ($data['payslip_adjustments'] as $adjustment) {
                if ($adjustment['type'] == PayslipAdjustmentTypeEnum::BENEFIT->value) {
                    $netPay += $adjustment['value'] ?? 0;
                } else {
                    $netPay -= $adjustment['value'] ?? 0;
                }
                $adjustments[] = [
                    'payslip_id' => $payslip->id,
                    'amount' => $adjustment['value'] ?? 0,
                    'reason' => $adjustment['reason'] ?? null,
                    'type' => $adjustment['type'],
                ];
            }
        }

        PayslipAdjustmentRepository::make()->insert($adjustments);

        $details['earnings'] = $data['details']['earnings'] ?? [];
        $details['deductions'] = $data['details']['deductions'] ?? [];

        $data['details'] = $details;
        $data['net_pay'] = $netPay;
        $data['status'] = PayslipStatusEnum::DRAFT->value;
        $data['error'] = null;
        $data['edited_manually'] = true;

        $payslip = $this->repository->update($data, $payslip, $relationships, $countable);
        $payslip->payrun?->calculatePaymentCost();
        if ($payslip->payrun->erroredPayslips()->count() <= 0) {
            PayrunService::make()->update([
                'has_errors' => false,
            ], $payslip->payrun);
        }
        return $payslip->refresh()->load($relationships)->loadCount($countable);
    }

    /**
     * @throws Throwable
     */
    public function addAdjustment(int $payslipId, array $data, array $relations = []): ?Payslip
    {
        $payslip = $this->repository->find($payslipId);

        if (!$payslip) {
            return null;
        }
        if (!$payslip->canUpdate()) {
            return null;
        }

        throw_if(
            $data['type'] === PayslipAdjustmentTypeEnum::DEDUCTION->value && $payslip->net_pay < $data['amount'],
            ValidationException::withMessages(['amount' => "Amount is greater than pay slip's net pay"])
        );

        if ($payslip->payrun?->status != PayrunStatusEnum::DRAFT->value) {
            return null;
        }

        $data['payslip_id'] = $payslipId;
        PayslipAdjustmentRepository::make()
            ->create($data);

        $payslip->refresh()->calculateNetPay();
        $payslip->payrun->calculatePaymentCost();

        return $payslip->load($relations);
    }

    public function toggleStatus($payslipId, string $status): ?string
    {
        $payslip = $this->repository->find($payslipId, ['payrun']);

        if (!$payslip) {
            return null;
        }

        if (!$payslip->canUpdate()) {
            return null;
        }

        if (!isAdmin() && $payslip->user_id != user()->id) {
            return null;
        }

        if ($payslip->status == $status) {
            return $status;
        }

        $payrun = $payslip->payrun;
        if ($payrun?->status != PayrunStatusEnum::DRAFT->value) {
            return null;
        }

        $payslip->update([
            'status' => $status,
        ]);

        $payrun->calculatePaymentCost();

        if ($payrun->payslips()->where('status', '!=', PayrunStatusEnum::APPROVED->value)->count() <= 0) {
            $payrun->update([
                'status' => PayrunStatusEnum::APPROVED->value,
            ]);
        }

        return $status;
    }

    public function getByPayrun($payrunId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByPayrun($payrunId, $relations, $countable);
    }

    public function getPdfData(Payslip $payslip): array
    {
        $earnings = $payslip->details['earnings'] ?? [];
        $deductions = $payslip->details['deductions'] ?? [];
        $totalEarnings = array_reduce($earnings, fn($carry, $item) => $carry + ($item['value'] ?? 0), 0);
        $totalDeductions = array_reduce($deductions, fn($carry, $item) => $carry + ($item['value'] ?? 0), 0);

        $payslip->benefits->each(function (PayslipAdjustment $payslipAdjustment) use (&$totalEarnings, &$earnings) {
            $this->getTotalWithLabeledDetails($payslipAdjustment, $totalEarnings, $earnings);
        });

        $payslip->deductions->each(function (PayslipAdjustment $payslipAdjustment) use (&$totalDeductions, &$deductions) {
            $this->getTotalWithLabeledDetails($payslipAdjustment, $totalDeductions, $deductions);
        });

        Carbon::setLocale(app()->getLocale());
        $payPeriod = Carbon::parse($payslip->payrun->payment_date)->monthName . " , " . Carbon::parse($payslip->payrun->payment_date)->year;
        return [
            'date_of_joining' => $payslip->user?->created_at?->format('Y-m-d'),
            'pay_period' => $payPeriod,
            'worked_days' => $payslip->paid_days,
            'full_name' => $payslip->user?->full_name,
            'earnings' => $earnings ?? 0,
            'total_earnings' => $totalEarnings ?? 0,
            'deductions' => $deductions ?? 0,
            'total_deductions' => $totalDeductions ?? 0,
            'total_pay' => $payslip->net_pay ?? 0,
            'role' => $payslip->user?->roles?->first()?->name ?? null,
        ];
    }

    private function getTotalWithLabeledDetails(PayslipAdjustment $payslipAdjustment, float|int &$totalEarnings, array &$detailsArray = []): void
    {
        $detailsArray[] = [
            'label' => $payslipAdjustment->reason,
            'value' => $payslipAdjustment->amount,
            'errors' => null
        ];
        $totalEarnings += $payslipAdjustment->amount;
    }

    public function toPdf($payslipId): ?string
    {
        $payslip = $this->repository->find($payslipId, ['user', 'user.roles', 'benefits', 'deductions', 'formula', 'formula.formulaSegments', 'payrun']);

        if (!$payslip) {
            return null;
        }

        if (!$payslip->canDownload()) {
            return null;
        }

        $data = $this->getPdfData($payslip);
        $data['company_name'] = trans('site.center_name');
        return PDF::viewToPdf(view('pdf.pay-slip', $data));
    }

    public function bulkPdfDownload(array $filterData = []): string
    {
        $data = [];
        $total = 0;
        $customFilters = $filterData['custom_filter'] ?? null;
        $this->repository
            ->globalQuery(['user', 'user.roles', 'benefits', 'deductions', 'formula', 'formula.formulaSegments', 'payrun'])
            ->when($customFilters != 'all', fn($query) => $query->whereIn('id', $filterData['payslip_ids'] ?? []))
            ->where('payrun_id', $filterData['payrun_id'])
            ->chunk(10,
                /**
                 * @param Collection|DBCollection<Payslip>|array<Payslip> $payslips
                 * @throws Exception
                 */
                function (Collection|DBCollection|array $payslips) use (&$data, &$total) {
                    foreach ($payslips as $payslip) {
                        if ($payslip->canDownload()) {
                            $pdfData = $this->getPdfData($payslip);
                            $data[] = $pdfData;
                            $total += $pdfData['total_pay'] ?? 0;
                        }
                    }
                });

        return PDF::viewToPdf(view('pdf.multiple-pay-slips', [
            'data' => $data,
            'total' => $total,
            'payslips' => trans('site.center_name'),
        ]));
    }

    public function bulkAdjustment(array $data): int
    {
        $payrunId = $data['payrun_id'];
        $payrun = PayrunRepository::make()->find($payrunId);

        if (isset($data['formulas']) && count($data['formulas'])) {
            $payslips = $this->repository->getByFormulaAndPayrun($payrunId, $data['formulas'], ['payrun']);
        } elseif (isset($data['payslips_ids']) && count($data['payslips_ids'])) {
            $payslips = $this->repository->getByIdsAndPayrun($payrunId, $data['payslips_ids'], ['payrun']);
        } else {
            $payslips = $this->repository->getAllByPayrun($payrunId, ['payrun']);
        }

        $updatedCount = 0;
        $payslips->each(function (Payslip $payslip) use ($data, &$updatedCount) {
            if (!$payslip->canUpdate()) {
                return true;
            }

            if ($data['type'] === PayslipAdjustmentTypeEnum::DEDUCTION->value && $payslip->net_pay < $data['amount']) {
                return true;
            }

            PayslipAdjustmentRepository::make()->create([
                'payslip_id' => $payslip->id,
                'amount' => $data['amount'],
                'type' => $data['type'],
                'reason' => $data['reason'] ?? "",
            ]);
            $payslip->refresh()->calculateNetPay();
            $updatedCount++;
            return true;
        });

        $payrun->refresh()->calculatePaymentCost();

        return $updatedCount;
    }

    public function mine(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByUser(\user()->id, $relations, $countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var Payslip $payslip */
        $payslip = parent::view($id, $relationships, $countable);

        return isAdmin()
            ? $payslip
            : (isDoctor() && $payslip?->user_id != user()->id ? null : $payslip);
    }
}
