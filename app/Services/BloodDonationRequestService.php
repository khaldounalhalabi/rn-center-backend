<?php

namespace App\Services;

use App\Models\BloodDonationRequest;
use App\Repositories\BloodDonationRequestRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<BloodDonationRequest>
 * @property BloodDonationRequestRepository $repository
 */
class BloodDonationRequestService extends BaseService
{
    use Makable;

    public string $repositoryClass = BloodDonationRequestRepository::class;
}
