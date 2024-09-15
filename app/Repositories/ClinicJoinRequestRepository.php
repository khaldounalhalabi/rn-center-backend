<?php

namespace App\Repositories;

use App\Models\ClinicJoinRequest;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<ClinicJoinRequest>
 */
class ClinicJoinRequestRepository extends BaseRepository
{
    protected string $modelClass = ClinicJoinRequest::class;
}
