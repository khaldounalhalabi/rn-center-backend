<?php

namespace App\Exceptions;

use Exception;

class FormulaSyntaxException extends Exception
{
    public int $line;
    public int $position;

    public function __construct(string $message = "Syntax Error", int $line = 0, int $position = 0)
    {
        $this->line = $line;
        $this->position = $position;
        $message = str_replace(['Variable', 'IntegerLiteral', 'Double'], ['defined formula variable', 'a number', ''], $message);

        parent::__construct($message . "  line : $line in col : $position");
    }
}
