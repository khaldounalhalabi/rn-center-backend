<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum FormulaParsingFlagEnum: string
{
    use BaseEnum;

    case DIVIDING_BY_ZERO = "dividing_by_zero";
    case FORMULA_SYNTAX = "formula_syntax";
    case UNKNOWN = "unknown";
    case WRONG_ATTENDANCE_LOGS_ORDER = "wrong_attendance_logs_order";
}
