<?php

namespace App\Services;

use App\Models\BloodDonationRequest;
use App\Repositories\BloodDonationRequestRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<BloodDonationRequest>
 * @property BloodDonationRequestRepository $repository
 */
class BloodDonationRequestService extends BaseService
{
    use Makable;

    public string $repositoryClass = BloodDonationRequestRepository::class;

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var BloodDonationRequest $donation */
        $donation = parent::view($id, $relationships, $countable);

        if (!$donation?->can_wait_until?->isAfter(now())) {
            return null;
        }

        return $donation;
    }
}
