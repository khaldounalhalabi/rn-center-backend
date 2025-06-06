<?php

namespace App\Services\v1\Payrun;

use App\Enums\PayrunStatusEnum;
use App\Enums\PayslipStatusEnum;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\EquationParser;
use App\FormulaParser\Result;
use App\Jobs\ProcessPayrunJob;
use App\Models\Formula;
use App\Models\FormulaVariable;
use App\Models\Payrun;
use App\Models\Payslip;
use App\Models\User;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\FormulaVariableRepository;
use App\Repositories\PayrunRepository;
use App\Repositories\PayslipRepository;
use App\Services\Contracts\BaseService;
use App\Services\v1\Payslip\PayslipService;
use App\Traits\Makable;
use Carbon\Carbon;

/**
 * @extends BaseService<Payrun>
 * @property PayrunRepository $repository
 */
class PayrunService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PayrunRepository::class;

    /**
     * @param array|null $data
     * @return Payrun|bool|null
     */
    public function create(?array $data = null): Payrun|bool|null
    {
        if (!empty($data['from']) && !empty($data['to'])) {
            $shouldDeliveredAt = Carbon::parse($data['to']);
            $period = [
                Carbon::parse($data['from'])->format('Y-m-d'),
                Carbon::parse($data['to'])->format('Y-m-d'),
            ];
        } else {
            $period = [
                Carbon::parse($data['data'])->startOfMonth()?->format('Y-m-d'),
                Carbon::parse($data['data'])->endOfMonth()->format('Y-m-d')
            ];

            $shouldDeliveredAt = Carbon::parse($period[1]);
        }

        $paymentDate = $shouldDeliveredAt->monthName . " , " . $shouldDeliveredAt->year;

        if ($this->repository->checkForPeriodOverlap($period[0], $period[1]) && !$data['force_create']) {
            return false;
        }

        $payrun = $this->repository->create([
            'status' => PayrunStatusEnum::DRAFT->value,
            'should_delivered_at' => $shouldDeliveredAt,
            'payment_date' => $paymentDate,
            'payment_cost' => 0,
            'period' => implode(' - ', $period),
            'from' => $period[0],
            'to' => $period[1],
        ]);

        ProcessPayrunJob::dispatch($payrun);

        return $payrun->refresh();
    }

    /**
     * Warning: when using this method you need to know that it just create the corresponding payslip for the provided
     * employee and pay run so after that remember to update the pay run payment_cost
     * @param Payrun $payrun
     * @param User   $user
     * @return Payslip|null
     */
    public function apply(Payrun $payrun, User $user): ?Payslip
    {
        if (!$payrun->canUpdate()) {
            return null;
        }

        $formula = $user->formula;
        if (!$formula) {
            return null;
        }

        $equationResult = EquationParser::parse($formula->formula)
            ?->resolve($user->id, $formula, $payrun->from, $payrun->to);

        if (!$equationResult) {
            $errors = collect(Expression::getParsingFlags())
                ->map(fn($error) => $error->toArray())
                ->unique('flag');
            $equationResult = new Result();
        } elseif ($equationResult->hasErrors()) {
            $errors = collect($equationResult->getErrors())
                ->map(fn($error) => $error->toArray())
                ->unique('flag');
        }

        [$earnings, $deductions] = PayslipService::getPayslipSegmentsDetails($user, $formula, $payrun);
        $variables = $this->getVariablesValues($user, $formula, $payrun->from, $payrun->to);

        $payslip = $payrun->payslips->firstWhere('user_id', $user->id);
        $payslipData = [
            'formula_id' => $formula->id,
            'paid_days' => AttendanceLogRepository::make()->getInRangeAttendanceCount($user->id, $payrun->from, $payrun->to),
            'gross_pay' => $equationResult->getResult(),
            'net_pay' => $equationResult->getResult(),
            'status' => isset($errors) ? PayslipStatusEnum::FAILED->value : PayslipStatusEnum::DRAFT->value,
            'error' => $errors ?? null,
            'details' => [
                'earnings' => $earnings ?? [],
                'deductions' => $deductions ?? [],
                'variables_values' => $variables,
            ],
        ];
        if (!$payslip) {
            $payslip = PayslipRepository::make()
                ->create([
                    'payrun_id' => $payrun->id,
                    'user_id' => $user->id,
                    ...$payslipData
                ], ['formula']);
        } else {
            $payslip = PayslipRepository::make()->update($payslipData, $payslip);
        }

        return $payslip;
    }

    public function toggleStatus($payrunId, string $status): ?string
    {
        $payrun = $this->repository->find($payrunId);

        if (!$payrun) {
            return null;
        }

        if (!$payrun->canUpdate()) {
            return null;
        }

        if ($payrun->status == $status) {
            return $status;
        }

        if ($status == PayrunStatusEnum::DONE->value) {
            PayslipRepository::make()->changeStatusByPayrunWhereNotExcluded($payrun->id, PayslipStatusEnum::DONE->value);
        }

        if (
            $payrun->payslips()->where('status', PayslipStatusEnum::REJECTED->value)->count()
            && (in_array($status, [
                PayrunStatusEnum::DONE->value,
                PayrunStatusEnum::APPROVED->value
            ]))
        ) {
            return null;
        }

        $this->repository->update([
            'status' => $status,
        ], $payrun);

        if ($status == PayrunStatusEnum::DRAFT->value || $status == PayrunStatusEnum::APPROVED->value) {
            PayslipRepository::make()->changeStatusByPayrunWhereNotExcluded($payrun->id, PayslipStatusEnum::DRAFT->value);
        }

        return $status;
    }

    public function reprocessPayrun($payrunId)
    {
        $payrun = $this->repository->find($payrunId, ['payslips']);

        if (!$payrun) {
            return null;
        }

        if (!$payrun->canUpdate()) {
            return null;
        }

        ProcessPayrunJob::dispatch($payrun);

        return $payrun->refresh();
    }

    public function reportToExcel($payrunId)
    {
        $payrun = $this->repository->find($payrunId);

        if (!$payrun) {
            return null;
        }

        $ids = $payrun->payslips()->pluck('id')->toArray();

        return PayslipRepository::make()->export($ids);
    }

    public function delete($id): ?bool
    {
        $payrun = $this->repository->find($id);

        if (!$payrun) {
            return null;
        }

        if (!$payrun->canDelete()) {
            return null;
        }

        return $this->repository->delete($payrun);
    }

    public function getVariablesValues(User $user, Formula $formula, Carbon $from, Carbon $to): array
    {
        $system = FormulaVariableRepository::make()->all();

        $variables = [];
        /** @var FormulaVariable $sysVar */
        foreach ($system as $sysVar) {
            $variables[$sysVar?->slug] = [
                'label' => $sysVar->name?->translate("en"),
                'value' => $sysVar->resolve($user->id, $formula, $from, $to)->getResult(),
            ];
        }

        return $variables;
    }
}
