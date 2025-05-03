<?php

namespace App\Excel\Exporters;

use App\Excel\BaseExporter;
use Carbon\Carbon;

class AttendanceLogExport extends BaseExporter
{
    public function cast(mixed $value, ?string $colName = null)
    {
        if ($colName === "attend_at" && $value instanceof Carbon) {
            return $value->format("Y-m-d H:i:s");
        }

        return parent::cast($value, $colName);
    }
}
