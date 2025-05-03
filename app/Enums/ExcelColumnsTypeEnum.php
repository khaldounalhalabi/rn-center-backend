<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum ExcelColumnsTypeEnum: string
{
    use BaseEnum;

    case STRING = "string";
    case NUMERIC = "numeric";
    case BOOLEAN = "boolean";
    case DATE = "Y-m-d";
    case DATE_TIME = "Y-m-d H:i";
    case TIME = "H:i";
    case LIST = "list";
}
