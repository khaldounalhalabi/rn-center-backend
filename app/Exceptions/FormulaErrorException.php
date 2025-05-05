<?php

namespace App\Exceptions;

use App\Enums\FormulaParsingFlagEnum;
use App\Models\Formula;
use Exception;

class FormulaErrorException extends Exception
{
    public FormulaParsingFlagEnum $errorFlag = FormulaParsingFlagEnum::UNKNOWN;
    public $message;

    /**
     * @param Formula|null $formula
     */
    public function __construct(?Formula $formula = null)
    {
        parent::__construct();
        $this->message = "Unknown error while parsing formula , please contact support";
    }
}
