<?php

namespace App\Excel;

use App\Enums\ExcelColumnsTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @template T
 */
class BaseExporter implements FromCollection, WithMapping, WithHeadings, WithCustomChunkSize, ShouldAutoSize, WithStyles, WithEvents, SkipsEmptyRows
{
    /** @var Collection<T>|EloquentCollection<T> */
    public Collection|EloquentCollection $collection;

    /** @var T|null */
    public ?Model $model = null;

    public ?array $requestCols = null;

    public bool $isExample = false;

    protected array $exportables = [];
    protected array $importExample = [];
    protected array $headings = [];
    protected array $mergedHeadings = [];


    /**
     * @param T[]|Collection<T>|EloquentCollection<T> $collection
     * @param Model                                   $model
     * @param array|null                              $requestCols
     * @param bool                                    $isExample
     */
    public function __construct(array|Collection|EloquentCollection $collection, Model $model, ?array $requestCols = null, bool $isExample = false)
    {
        $this->collection = $collection;
        $this->model = $model;
        $this->requestCols = $requestCols;
        $this->isExample = $isExample;

        $this->importExample = !method_exists($this->model, 'importExample')
            ? []
            : $this->model->importExample();

        $this->exportables = method_exists($this->model, 'exportable')
            ? $this->model->exportable()
            : [];

        $this->headings = $this->isExample ? $this->importExample : $this->exportables;
    }

    public function collection()
    {
        if (method_exists($this->model, 'export')) {
            return $this->model->export();
        }

        if ($this->isExample) {
            return collect([$this->getExampleData()]);
        }

        $mergeToCollection = collect($this->mergeCollection($this->collection));
        if ($mergeToCollection->count()) {
            return $this->collection->merge(collect($this->mergeCollection($this->collection)));
        } else {
            return $this->collection;
        }
    }

    public function map($row): array
    {
        if ($this->isExample) {
            return array_values($row);
        }

        $map = [];

        foreach (array_keys($this->exportables) as $col) {
            if ($this->requestCols && !in_array($col, $this->requestCols)) {
                continue;
            }

            $val = $this->getNestedValue($row, $col);
            $map[] = $this->cast($val, $col);
        }

        return array_merge($map, $this->mergeMap($row));
    }

    public function headings(): array
    {
        $headings = array_keys(array_merge($this->headings, $this->mergeHeadings()));

        if ($this->requestCols) {
            $headings = collect($headings)->intersect($this->requestCols)->toArray();
        }

        foreach ($headings as $key => $head) {
            $headings[$key] = Str::title(Str::replace(['.', '-', '_'], ' ', $head));
        }

        return $headings;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function mergeHeadings(): array
    {
        return [];
    }

    public function mergeCollection(Collection|EloquentCollection $collection): Collection|array
    {
        return [];
    }

    public function mergeMap($row): array
    {
        return [];
    }

    public function cast(mixed $value, ?string $colName = null)
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            if ($value) return "true";
            else return "false";
        }

        return $this->additionalCasting($value);
    }

    public function additionalCasting(mixed $value)
    {
        return $value;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_YELLOW],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        $columns = $this->isExample ? $this->importExample : $this->exportables;


        $columns = array_merge($columns, $this->mergeHeadings());

        if (!Arr::isAssoc($columns)) {
            return [];
        }

        return [
            AfterSheet::class => function (AfterSheet $event) use ($columns) {
                $sheet = $event->sheet->getDelegate();
                $asciiIndex = 65; // A
                foreach ($columns as $type) {
                    $listValues = [];
                    if (is_array($type)) {
                        $listValues = $type;
                        $type = ExcelColumnsTypeEnum::LIST;
                    }

                    $columnLetter = chr($asciiIndex);
                    $range = "{$columnLetter}2:{$columnLetter}1048576";
                    $validation = new DataValidation();
                    $validation->setType(match ($type) {
                        ExcelColumnsTypeEnum::STRING => DataValidation::TYPE_TEXTLENGTH,
                        ExcelColumnsTypeEnum::DATE, ExcelColumnsTypeEnum::DATE_TIME => DataValidation::TYPE_DATE,
                        ExcelColumnsTypeEnum::TIME => DataValidation::TYPE_TIME,
                        ExcelColumnsTypeEnum::BOOLEAN, ExcelColumnsTypeEnum::LIST => DataValidation::TYPE_LIST,
                        ExcelColumnsTypeEnum::NUMERIC => DataValidation::TYPE_DECIMAL,
                        default => DataValidation::TYPE_WHOLE
                    });

                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);

                    if ($type == ExcelColumnsTypeEnum::BOOLEAN || $type == ExcelColumnsTypeEnum::LIST) {
                        $validation->setShowDropDown(true);

                        if ($type == ExcelColumnsTypeEnum::BOOLEAN) {
                            $validation->setFormula1('"' . implode(',', ["true", "false"]) . '"');
                        } else {
                            $validation->setFormula1('"' . implode(',', $listValues) . '"');
                        }
                    }

                    $sheet->setDataValidation($range, $validation);
                    $asciiIndex++;
                }
            },
        ];
    }

    public function getExampleData(): array
    {
        $data = [];
        foreach ($this->importExample as $col => $type) {
            $data[$col] = $this->getValueFromExampleType($col, $type);
        }
        return $data;
    }

    public function getValueFromExampleType($col, $type)
    {
        if (is_array($type)) {
            return fake()->randomElement($type);
        } else {
            return match ($type) {
                ExcelColumnsTypeEnum::BOOLEAN => fake()->randomElement(["true", "false"]),
                ExcelColumnsTypeEnum::DATE => now()->format('Y-m-d'),
                ExcelColumnsTypeEnum::DATE_TIME => now()->format('Y-m-d H:i'),
                ExcelColumnsTypeEnum::TIME => now()->format('H:i'),
                ExcelColumnsTypeEnum::NUMERIC => fake()->numberBetween(1, 10),
                default => $this->getStringColumnExampleValue($col),
            };
        }
    }

    protected function getStringColumnExampleValue($col): string
    {
        if (Str::contains($col, ["first_name", "last_name", "name"])) {
            if (str_contains($col, "last")) {
                return fake()->lastName;
            } elseif (str_contains($col, "first_name")) {
                return fake()->firstName;
            } else {
                return fake()->name;
            }
        } elseif (Str::contains($col, ['identifier', 'ID'])) {
            return "ID-" . fake()->numberBetween(1000, 9999);
        } elseif (Str::contains($col, ['phone', 'phone_number'])) {
            return fake()->phoneNumber();
        } elseif (Str::contains($col, ['email', 'email_address'])) {
            return fake()->email();
        } elseif (Str::contains($col, ['address', 'home_address', 'street_address'])) {
            return fake()->address();
        } else {
            return fake()->word();
        }
    }

    /**
     * Get a nested value from an object or array using dot notation
     * @param mixed $target The object or array to extract value from
     * @param string $path The path in dot notation (e.g., "user.address.street")
     * @return mixed The value found at the specified path
     */
    protected function getNestedValue(mixed $target, string $path): mixed
    {
        $segments = explode('.', $path);
        $current = $target;

        foreach ($segments as $segment) {
            if (is_array($current)) {
                $current = $current[$segment] ?? null;
            } elseif (is_object($current)) {
                $current = $current->{$segment} ?? null;
            } else {
                return null;
            }

            if ($current === null) {
                return null;
            }
        }

        return $current;
    }
}
