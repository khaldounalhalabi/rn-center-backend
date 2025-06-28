<?php

namespace App\Repositories;

use App\Models\PatientStudy;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<PatientStudy>
 */
class PatientStudyRepository extends BaseRepository
{
    protected string $modelClass = PatientStudy::class;
}
