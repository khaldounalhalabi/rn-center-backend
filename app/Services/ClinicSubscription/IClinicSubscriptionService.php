<?php

namespace App\Services\ClinicSubscription;

use App\Models\ClinicSubscription;
use App\Services\Contracts\IBaseService;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends IBaseService<ClinicSubscription>
 * Interface IUserService
 */
interface IClinicSubscriptionService extends IBaseService
{
    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    #[ArrayShape(['data' => "mixed", 'pagination_data' => "array"])]
    public function getClinicSubscriptions($clinicId, array $relations = [], int $perPage = 10): ?array;
}
