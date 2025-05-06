<?php

namespace App\Traits;


use App\Models\Formula;
use App\Models\FormulaSegment;
use App\Models\FormulaVariable;

trait HasFormulaString
{
    private function getBaseName()
    {
        if ($this instanceof FormulaSegment) {
            return $this->segment;
        }  elseif ($this instanceof FormulaVariable) {
            return $this->slug;
        } elseif ($this instanceof Formula) {
            return $this->formula;
        } else {
            return '';
        }
    }

    public function getTitle(): string
    {
        $formattedString = preg_replace('/_\d+/', '', $this->getBaseName());
        $formattedString = str_replace('_', ' ', $formattedString);
        $formattedString = preg_replace('/\s+/', ' ', $formattedString);
        $formattedString = str_replace(['+', '-'], ' ', $formattedString);
        $formattedString = ucwords($formattedString);
        if (is_array($formattedString)) {
            return implode(' ', $formattedString);
        } elseif (is_null($formattedString)) {
            return '';
        } else {
            return $formattedString;
        }
    }
}
