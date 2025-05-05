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

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data = []): bool
    {
        return FormulaSegment::insert($data);
    }
}
