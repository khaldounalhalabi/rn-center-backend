<?php

namespace App\Services\v1\ClinicJoinRequest;

use App\Models\ClinicJoinRequest;
use App\Repositories\ClinicJoinRequestRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<ClinicJoinRequest>
 *
 * @property ClinicJoinRequestRepository $repository
 */
class ClinicJoinRequestService extends BaseService
{
    use Makable;

    protected string $repositoryClass = ClinicJoinRequestRepository::class;
}
