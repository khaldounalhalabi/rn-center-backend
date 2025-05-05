<?php

namespace App\Traits;

use App\Exceptions\UndefinedVariableInFormula;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\SystemVariables\SystemVariable;
use App\Models\Formula;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

/**
 * @mixin Identifier
 */
trait IdentifierResolver
{
    /**
     * @throws UndefinedVariableInFormula
     */
    public function resolveSystemVariables($userId, Formula $formula, Carbon|string $from, Carbon|string $to): float|int
    {
        $result = SystemVariable::factory(
            $this->variable->slug,
            UserRepository::make()->find($userId),
            AttendanceLogRepository::make()->getInRange($userId, $from, $to),
            $from,
            $to
        );

        if (!$result) {
            throw new UndefinedVariableInFormula($this->name, $formula);
        }

        return $result->getResult();
    }
}
