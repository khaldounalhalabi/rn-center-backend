<?php

namespace App\Repositories;

use App\Models\FormulaVariable;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<FormulaVariable>
 */
class FormulaVariableRepository extends BaseRepository
{
    protected string $modelClass = FormulaVariable::class;
}
