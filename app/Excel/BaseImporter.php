<?php

namespace App\Excel;

use App\Casts\Translatable;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

/**
 * @template T of Model
 */
class BaseImporter implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{

    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function mapping(): array
    {
        return array_keys(
            method_exists($this->model, 'importable')
                ? $this->model->importable()
                : $this->model->getFillable()
        );
    }

    /**
     * @param array $row
     * @return Model|array|null
     */
    public function model(array $row): Model|array|null
    {
        if (is_a($this, ToCollection::class)) {
            return null;
        }

        if (!method_exists($this->model, 'import')) {
            $import = [];

            foreach ($this->mapping() as $col) {
                if (isset($row[$col])) {
                    $import[$col] = $this->processRow($col, $row[$col]);
                }
            }

            $modelClass = get_class($this->model);
            $createdModel = new $modelClass(array_merge($import, $this->mergeImported($row)));
            $createdModel->save();

            $this->additionalImporting($row, $createdModel);

            return $createdModel;
        }
        return $this->model->import($row);
    }

    /**
     * @param string $colName
     * @param mixed  $row
     * @return mixed
     */
    protected function processRow(string $colName, mixed $row): mixed
    {
        $casts = $this->model->getCasts();
        if (in_array($colName, array_keys($casts)) && $casts[$colName] == Translatable::class && Str::isJson($row)) {
            return json_encode([
                app()->getLocale() => $row,
            ], JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
        }

        if (
            (in_array($colName, array_keys($casts)) && in_array($casts[$colName], ['bool', 'boolean']))
            || (in_array($row, ['true', 'false']))
        ) {
            if ($row == "1" || $row == "true" || $row == 1) {
                return true;
            } else {
                return false;
            }
        }

        if (isset($casts[$colName])
            && (in_array($casts[$colName], ['date', 'datetime']) || str_contains($casts[$colName], "date"))
        ) {
            return $this->getDateOrTimeFromExcelIfAvailable($row);
        }

        return $this->additionalProcessing($colName, $row);
    }

    public function mergeImported($row): array
    {
        return [];
    }

    /**
     * @param       $row
     * @param Model $createdModel
     * @return void
     */
    public function additionalImporting($row, Model $createdModel): void
    {
        return;
    }

    /**
     * @param string $colName
     * @param mixed  $row
     * @return mixed
     */
    protected function additionalProcessing(string $colName, mixed $row): mixed
    {
        return $row;
    }

    /**
     * @param mixed $row
     * @return Carbon|mixed
     */
    public function getDateOrTimeFromExcelIfAvailable(mixed $row): mixed
    {
        try {
            $value = Carbon::parse(Date::excelToDateTimeObject($row));
        } catch (Exception|Error|Throwable) {
            try {
                $value = Carbon::parse($row);
            } catch (Exception|Error|Throwable) {
                $value = $row;
            }
        }

        return $value;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
