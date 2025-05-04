<?php

namespace App\Services\v1\Formula;

use App\Models\Formula;
use App\Repositories\FormulaRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Formula>
 * @property FormulaRepository $repository
 */
class FormulaService extends BaseService
{
    use Makable;

    protected string $repositoryClass = FormulaRepository::class;
}
