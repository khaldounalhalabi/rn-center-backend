<?php

namespace App\Services\v1\FormulaVariable;

use App\Models\FormulaVariable;
use App\Repositories\FormulaVariableRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<FormulaVariable>
 *
 * @property FormulaVariableRepository $repository
 */
class FormulaVariableService extends BaseService
{
    use Makable;

    protected string $repositoryClass = FormulaVariableRepository::class;
}
