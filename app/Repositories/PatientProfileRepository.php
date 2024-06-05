<?php

namespace  App\Repositories;

use App\Models\PatientProfile;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<PatientProfile>
 */
class PatientProfileRepository extends BaseRepository
{
    public function __construct(PatientProfile $patientProfile)
    {
        parent::__construct($patientProfile);
    }
}
