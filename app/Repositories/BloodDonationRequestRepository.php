<?php

namespace App\Repositories;

use App\Models\BloodDonationRequest;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<BloodDonationRequest>
 */
class BloodDonationRequestRepository extends BaseRepository
{
    protected string $modelClass = BloodDonationRequest::class;
}
