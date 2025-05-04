<?php

namespace App\Repositories;

use App\Models\FormulaSegment;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<FormulaSegment>
 */
class FormulaSegmentRepository extends BaseRepository
{
    protected string $modelClass = FormulaSegment::class;
}
