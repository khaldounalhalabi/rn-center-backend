<?php

namespace App\FormulaParser\AntlrParser;

use Antlr\Antlr4\Runtime\Atn\ATNConfigSet;
use Antlr\Antlr4\Runtime\Dfa\DFA;
use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
use Antlr\Antlr4\Runtime\Error\Listeners\BaseErrorListener;
use Antlr\Antlr4\Runtime\Parser;
use Antlr\Antlr4\Runtime\Recognizer;
use Antlr\Antlr4\Runtime\Utils\BitSet;
use App\Exceptions\FormulaSyntaxException;
use Illuminate\Validation\ValidationException;

class ErrorListener extends BaseErrorListener
{
    public function __construct()
    {
    }

    public function syntaxError(Recognizer $recognizer, ?object $offendingSymbol, int $line, int $charPositionInLine, string $msg, ?RecognitionException $exception): void
    {
        throw new FormulaSyntaxException($msg , $line , $charPositionInLine);
    }

    public function reportAmbiguity(
        Parser       $recognizer,
        DFA          $dfa,
        int          $startIndex,
        int          $stopIndex,
        bool         $exact,
        ?BitSet      $ambigAlts,
        ATNConfigSet $configs,
    ): void
    {
        throw ValidationException::withMessages([
            'formula' => 'Please Resolve Ambiguity In Your Equation'
        ]);
    }

    public function reportAttemptingFullContext(
        Parser       $recognizer,
        DFA          $dfa,
        int          $startIndex,
        int          $stopIndex,
        ?BitSet      $conflictingAlts,
        ATNConfigSet $configs,
    ): void
    {
        throw ValidationException::withMessages([
            'formula' => 'Invalid Input'
        ]);
    }

    public function reportContextSensitivity(
        Parser       $recognizer,
        DFA          $dfa,
        int          $startIndex,
        int          $stopIndex,
        int          $prediction,
        ATNConfigSet $configs,
    ): void
    {
        throw ValidationException::withMessages([
            'formula' => 'Invalid Input'
        ]);
    }
}
