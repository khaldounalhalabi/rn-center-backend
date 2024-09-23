<?php

namespace App\Repositories;

use App\Enums\SubscriptionStatusEnum;
use App\Models\ClinicSubscription;
use App\Repositories\Contracts\BaseRepository;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends  BaseRepository<ClinicSubscription>
 */
class ClinicSubscriptionRepository extends BaseRepository
{
    protected string $modelClass = ClinicSubscription::class;


    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    #[ArrayShape(['data' => "mixed", 'pagination_data' => "array"])]
    public function getByClinic($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations)
                ->where('clinic_id', $clinicId)
        );
    }

    public function deactivatePreviousSubscriptions($clinicId): void
    {
        $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->update([
                'status' => SubscriptionStatusEnum::IN_ACTIVE->value
            ]);
    }
}
