<?php

namespace App\Excel\Exporters;

use App\Excel\BaseExporter;

class AttendanceLogExampleExport extends BaseExporter
{
    public function collection()
    {
        if ($this->isExample) {
            return $this->collection;
        }
        return parent::collection();
    }

    public function map($row): array
    {
        if ($this->isExample) {
            $map = [];
            foreach ($this->importExample as $key => $value) {
                if ($key == "user_id") {
                    $map[] = $row['user_id'];
                } elseif ($key == "full_name") {
                    $map[] = $row['full_name'];
                } elseif ($key == "attend_at" && isset($row['attend_at'])) {
                    $map[] = $row['attend_at'];
                } elseif ($key == "role" && isset($row['role'])) {
                    $map[] = $row['role'];
                } else {
                    $map[] = "";
                }
            }
            return $map;
        }

        return parent::map($row);
    }
}
