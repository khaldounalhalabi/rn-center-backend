<?php

namespace App\Exceptions;

use App\Models\Formula;

class UndefinedVariableInFormula extends FormulaErrorException
{
    public function __construct(string $variableName, ?Formula $formula = null)
    {
        parent::__construct($formula);
        $this->message = "Undefined formula variable ($variableName) in formula named : ($formula?->name) formula : [$formula?->formula] ";
    }
}
