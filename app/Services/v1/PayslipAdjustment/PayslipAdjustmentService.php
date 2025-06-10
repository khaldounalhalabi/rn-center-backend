<?php

namespace App\Services\v1\PayslipAdjustment;

use App\Models\PayslipAdjustment;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Common\PayslipUpdatedNotification;
use App\Repositories\PayslipAdjustmentRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<PayslipAdjustment>
 * @property PayslipAdjustmentRepository $repository
 */
class PayslipAdjustmentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PayslipAdjustmentRepository::class;

    public function delete($id): ?bool
    {
        $item = $this->repository->find($id, ['payslip.payrun']);

        if (!$item) {
            return null;
        }

        if (!$item->canDelete()) {
            return null;
        }

        $payslip = $item->payslip;

        $this->repository->delete($item->id);

        $payslip->calculateNetPay();
        $payslip->payrun->calculatePaymentCost();

        NotificationBuilder::make()
            ->notification(PayslipUpdatedNotification::class)
            ->to($payslip->user)
            ->method(NotifyMethod::ONE)
            ->data(['payslip_id' => $payslip->id])
            ->send();

        return true;
    }
}
