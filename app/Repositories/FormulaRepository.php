<?php

namespace App\Repositories;

use App\Models\Formula;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Formula>
 */
class FormulaRepository extends BaseRepository
{
    protected string $modelClass = Formula::class;
}
