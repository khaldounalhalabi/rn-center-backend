<?php

namespace App\Jobs;

use App\Enums\PayrunStatusEnum;
use App\Models\Payrun;
use App\Models\User;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Common\NewPayrunAddedNotification;
use App\Repositories\UserRepository;
use App\Services\v1\Payrun\PayrunService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessPayrunJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payrun $payrun;

    /**
     * Create a new job instance.
     */
    public function __construct(Payrun $payrun)
    {
        $this->payrun = $payrun;
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->payrun->update([
            'status' => PayrunStatusEnum::PROCESSING->value,
        ]);

        Artisan::call('app:fix-attendance-logs-sequence', [
            'from' => $this->payrun->from->format('Y-m-d'),
            'to' => $this->payrun->to->format('Y-m-d'),
        ]);

        $payrunHasError = false;
        DB::transaction(function () use (&$payrunHasError) {
            UserRepository::make()
                ->globalQuery(['formula'])
                ->chunk(25,
                    function (Collection $users) use (&$payrunHasError) {
                        $users->each(
                            function (User $user) use (&$payrunHasError) {
                                $payslip = PayrunService::make()
                                    ->apply($this->payrun, $user);
                                if ($payslip?->error && !$payrunHasError) {
                                    $payrunHasError = true;
                                }
                                if ($payslip) {
                                    NotificationBuilder::make()
                                        ->data([
                                            'payrun_id' => $this->payrun?->id,
                                            'from' => $this->payrun?->from,
                                            'to' => $this->payrun?->to,
                                            'payslip_id' => $payslip?->id
                                        ])->to($payslip->user)
                                        ->method(NotifyMethod::ONE)
                                        ->notification(NewPayrunAddedNotification::class)
                                        ->send();
                                }
                            });
                    }
                );
        });

        $this->payrun->update([
            'status' => PayrunStatusEnum::DRAFT->value,
            'payment_cost' => $this->payrun->getPaymentCost(),
            'has_errors' => $payrunHasError,
            'processed_at' => now(),
        ]);
    }

    public function uniqueId(): string
    {
        return $this->payrun->id . "_" . $this->payrun->payment_date . "_" . $this->payrun->should_delivered_at;
    }

    public function failed(?Throwable $exception): void
    {
        logger()->info(".........Process Job Payrun Failed..................");
        logger()->error($exception->getMessage());
        logger()->error($exception->getFile());
        logger()->error($exception->getLine());
        logger()->info(".........Process Job Payrun Failed..................");
    }
}
