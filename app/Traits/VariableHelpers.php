<?php

namespace App\Traits;

use App\Enums\FormulaParsingFlagEnum;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\Errors\ParsingError;
use App\FormulaParser\Result;
use App\Models\Formula;
use App\Models\FormulaVariable;
use Carbon\Carbon;
use Error;
use Exception;
use Throwable;

/**
 * @mixin FormulaVariable
 */
trait VariableHelpers
{
    public function resolve(?int $userId, Formula $formula, string|Carbon $from, string|Carbon $to): Result
    {
        try {
            $identifier = new Identifier(
                $this->slug,
                $this,
                0,
                0
            );

            return $identifier->resolve($userId, $formula, $from, $to);
        } catch (Exception|Error|Throwable $exception) {
            return new Result(0, [
                new ParsingError(FormulaParsingFlagEnum::UNKNOWN, $exception->getMessage())
            ]);
        }
    }
}
