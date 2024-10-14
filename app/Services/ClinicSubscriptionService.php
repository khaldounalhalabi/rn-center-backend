<?php

namespace App\Services;

use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\ClinicSubscription;
use App\Repositories\ClinicRepository;
use App\Repositories\ClinicSubscriptionRepository;
use App\Repositories\ClinicTransactionRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends BaseService<ClinicSubscription>
 * @property ClinicSubscriptionRepository $repository
 */
class ClinicSubscriptionService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicSubscriptionRepository::class;
    private SubscriptionRepository $subscriptionRepository;


    public function init(): void
    {
        parent::__construct();
        $this->subscriptionRepository = SubscriptionRepository::make();
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        if ($data['type'] == SubscriptionTypeEnum::BOOKING_COST_BASED->value && !isset($data['deduction_cost'])) {
            return null;
        }

        $subscription = $this->subscriptionRepository->find($data['subscription_id']);

        if (!$subscription) {
            return null;
        }

        $data['start_time'] = now();
        $data['end_time'] = $subscription->period == -1
            ? now()->addYears(200)  // lifetime
            : ($subscription->dayUnit()
                ? now()->addDays($subscription->period)
                : now()->addMonths($subscription->period)
            );

        $data['status'] = SubscriptionStatusEnum::ACTIVE->value;

        $this->repository->deactivatePreviousSubscriptions($data['clinic_id']);

        return parent::store($data, $relationships, $countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $clinicSubscription = $this->repository->find($id);

        if (!$clinicSubscription) {
            return null;
        }

        if ($data['subscription_id'] != $clinicSubscription->subscription_id) {
            $subscription = $this->subscriptionRepository->find($data['subscription_id']);

            if (!$subscription) {
                return null;
            }

            $data['ends_at'] = $subscription->period == -1
                ? $clinicSubscription->start_time->addYears(200)  // lifetime
                : ($subscription->dayUnit()
                    ? $clinicSubscription->start_time->addDays($subscription->period)
                    : $clinicSubscription->start_time->addMonths($subscription->period)
                );

            $data['status'] = $data['ends_at']->isAfter(now())
                ? SubscriptionStatusEnum::ACTIVE->value
                : SubscriptionStatusEnum::IN_ACTIVE->value;
        }

        if (isset($data['type']) && $data['type'] == SubscriptionTypeEnum::MONTHLY_PAID_BASED->value) {
            $data['deduction_cost'] = 0;
        }

        return $this->repository->update($data, $clinicSubscription, $relationships);
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    #[ArrayShape(['data' => "mixed", 'pagination_data' => "array"])]
    public function getClinicSubscriptions($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $perPage);
    }

    /**
     * @param int|null $clinicId
     * @param int|null $clinicSubscriptionId
     * @return ClinicSubscription|null
     */
    public function makeItPaid(?int $clinicId = null, ?int $clinicSubscriptionId = null): ?ClinicSubscription
    {
        if ($clinicId) {
            $clinic = ClinicRepository::make()->find($clinicId);

            if (!$clinic?->hasActiveSubscription()) {
                return null;
            }

            $clinicSubscription = $clinic->activeSubscription;

            if ($clinicSubscription?->is_paid) {
                return null;
            }

            $subscription = $clinicSubscription->subscription;
        } elseif ($clinicSubscriptionId) {
            $clinicSubscription = $this->repository->find($clinicSubscriptionId, ['clinic', 'subscription']);

            if (!$clinicSubscription) {
                return null;
            }

            if ($clinicSubscription->is_paid) {
                return null;
            }

            $subscription = $clinicSubscription->subscription;

            $clinic = $clinicSubscription->clinic;
        } else {
            return null;
        }

        TransactionRepository::make()->create([
            'type' => TransactionTypeEnum::INCOME->value,
            'date' => now(),
            'actor_id' => auth()->user()?->id,
            'amount' => $subscription->cost,
            'description' => "A pay from {$clinic->name?->en} for its subscription",
        ]);

        ClinicTransactionRepository::make()->create([
            'amount' => $subscription->cost,
            'date' => now(),
            'type' => ClinicTransactionTypeEnum::OUTCOME->value,
            'clinic_id' => $clinic->id,
            'status' => ClinicTransactionStatusEnum::DONE->value,
            'notes' => "A pay for the system subscription",
        ]);

        $clinicSubscription->update([
            'is_paid' => true,
        ]);

        return $clinicSubscription;
    }
}
