<?php

namespace App\Services\PatientProfile;

use App\Models\PatientProfile;
use App\Repositories\PatientProfileRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IPatientProfileService<PatientProfile>
 * @extends BaseService<PatientProfile>
 */
class PatientProfileService extends BaseService implements IPatientProfileService
{
    /**
     * PatientProfileService constructor.
     * @param PatientProfileRepository $repository
     */
    public function __construct(PatientProfileRepository $repository)
    {
        parent::__construct($repository);
    }
}
