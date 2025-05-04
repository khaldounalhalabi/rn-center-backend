<?php

namespace App\Services\v1\FormulaSegment;

use App\Models\FormulaSegment;
use App\Repositories\FormulaSegmentRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<FormulaSegment>
 * @property FormulaSegmentRepository $repository
 */
class FormulaSegmentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = FormulaSegmentRepository::class;
}
