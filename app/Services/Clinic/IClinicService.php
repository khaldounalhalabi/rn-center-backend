<?php

namespace App\Services\Clinic;

use App\Models\Clinic;
use App\Services\Contracts\IBaseService;

/**
 * @extends IBaseService<Clinic>
 */
interface IClinicService extends IBaseService
{
    /**
     * @param $clinicId
     * @return array
     */
    public function getClinicAvailableTimes($clinicId): array;

    /**
     * @param $clinicId
     * @return string|null
     */
    public function toggleClinicStatus($clinicId): ?string;

    /**
     * @param       $subscriptionId
     * @param array $relations
     * @param array $countable
     * @param int   $perPage
     * @return ?array
     */
    public function getBySubscription($subscriptionId, array $relations = [], array $countable = [], int $perPage = 10): ?array;
}
