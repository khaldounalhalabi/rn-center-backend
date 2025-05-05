<?php

namespace App\FormulaParser\Errors;

use App\Enums\FormulaParsingFlagEnum;

class ParsingError
{
    public FormulaParsingFlagEnum $errorFlag;
    public string $message;

    /**
     * @param FormulaParsingFlagEnum $errorFlag
     * @param string                 $message
     */
    public function __construct(FormulaParsingFlagEnum $errorFlag, string $message)
    {
        $this->errorFlag = $errorFlag;
        $this->message = $message;
    }

    public function __toString(): string
    {
        return "[$this->errorFlag->value]" . " : " . $this->message . "\n";
    }

    public function toArray(): array
    {
        return [
            'flag' => $this->errorFlag->value,
            'message' => $this->message
        ];
    }
}
