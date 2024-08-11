<?php

namespace App\Repositories\Contracts;

use App\Enums\MediaTypeEnum;
use App\Excel\BaseExporter;
use App\Excel\BaseImporter;
use App\Traits\FileHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as RegularCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T of Model
 */
abstract class BaseRepository
{
    use FileHandler;

    private static $instance;
    /**
     * @var class-string
     */
    protected string $modelClass = Model::class;
    protected Model $model;
    private Filesystem $fileSystem;
    private array $filterKeys = [];
    private array $fileColumnsName = [];
    private array $modelTableColumns;
    private array $orderableKeys = [];
    private array $relationSearchableKeys = [];
    private array $searchableKeys = [];
    private array $customOrders = [];
    private string $tableName;
    protected bool $filtered = false;
    protected $perPage;

    /**
     * @param T $model
     */
    public function __construct()
    {
        $this->model = new $this->modelClass;
        $this->tableName = $this->model->getTable();

        if (method_exists($this->model, 'filesKeys')) {
            $this->fileColumnsName = $this->model->filesKeys();
        }

        if (method_exists($this->model, 'searchableArray')) {
            $this->searchableKeys = $this->model->searchableArray();
        }

        if (method_exists($this->model, 'relationsSearchableArray')) {
            $this->relationSearchableKeys = $this->model->relationsSearchableArray();
        }

        if (method_exists($this->model, 'filterArray')) {
            $this->filterKeys = $this->model->filterArray();
        }

        if (method_exists($this->model, 'customOrders')) {
            $this->customOrders = $this->model->customOrders();
        }

        $this->filtered = (request()->header('filtered') ?? request()->header('Filtered', false)) ?? false;

        if (!auth()->user()?->isAdmin()) {
            $this->filtered = true;
        }

        $this->modelTableColumns = $this->getTableColumns();
        $this->perPage = request('per_page', 10);
    }

    public function getTableColumns(): array
    {
        $table = $this->model->getTable();

        return Schema::getColumnListing($table);
    }

    public static function make(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (!(self::$instance instanceof static)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param array $relationships
     * @param array $countable
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function all(array $relationships = [], array $countable = []): Collection|array|RegularCollection
    {
        return $this->globalQuery($relationships)->get();
    }

    protected function unsetNullable(array $data = []): array
    {
        foreach ($data as $key => $value) {
            if ($value == null) {
                if ($key != "service_id") {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * @param array $relations
     * @param array $countable
     * @param bool  $defaultOrder
     * @return Builder|T
     */
    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder
    {
        $query = $this->model->with($relations)->withCount($countable);

        if (request()->method() == 'GET') {
            $query = $query->where(function (Builder $builder) {
                return $this->filterFields($builder);
            });
            $query = $query->where(function ($q) {
                return $this->addSearch($q);
            });
            $query = $this->orderQueryBy($query, $defaultOrder);
        }

        return $query;
    }

    protected function paginate(Builder $query, int $perPage = 10): ?array
    {
        $perPage = request('per_page', $perPage);
        $data = $query->paginate($perPage);
        if ($data->count()) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data),
            ];
        }

        return null;
    }

    /**
     * @param Builder $query
     * @return Builder|T
     */
    private function addSearch(Builder $query): Builder
    {
        $keyword = $this->unsetEmptyParams(request("search", null));

        if ($keyword) {
            if (count($this->searchableKeys) > 0) {
                foreach ($this->searchableKeys as $search_attribute) {
                    $query->orWhere("$this->tableName.{$search_attribute}", 'REGEXP', "(?i).*$keyword.*");
                }
            }

            if (count($this->relationSearchableKeys) > 0) {
                foreach ($this->relationSearchableKeys as $relation => $values) {
                    foreach ($values as $search_attribute) {
                        $query->orWhereRelation($relation, function (Builder $q) use ($relation, $keyword, $search_attribute) {
                            $relTable = $q->getModel()->getTable();
                            $q->where("{$relTable}.{$search_attribute}", 'REGEXP', "(?i).*$keyword.*");
                        });
                    }
                }
            }
            $query->orWhere($this->tableName . '.id', $keyword);
        }

        return $query;
    }

    /**
     * this function implement already defined filters in the model
     * @param Builder $query
     * @return Builder|T
     */
    private function filterFields(Builder $query): Builder
    {
        foreach ($this->filterKeys as $filterFields) {
            $field = $filterFields['field'] ?? $filterFields['name'];
            $operator = $filterFields['operator'] ?? "=";
            $relation = $filterFields['relation'] ?? null;
            $method = $filterFields['method'] ?? "where";
            $callback = $filterFields['query'] ?? null;
            $value = request($field);
            $range = is_array($value);
            $value = $this->unsetEmptyParams($value);
            if ($range && isset($value)) {
                $value = array_values($value);
            }

            if (!$value) {
                continue;
            }

            if ($callback && is_callable($callback)) {
                $query = call_user_func($callback, $query, $value);
            } elseif ($relation) {
                $tables = explode('.', $relation);
                $col = $tables[count($tables) - 1];
                unset($tables[count($tables) - 1]);
                $relation = implode('.', $tables);

                $query = $query->whereRelation($relation, function (Builder $q) use ($col, $relation, $range, $field, $method, $operator, $value) {
                    $relTable = $q->getModel()->getTable();
                    if ($range) {
                        return $this->handleRangeQuery($value, $q, $relTable, $col);
                    }
                    if ($operator === "like") {
                        return $q->{$method}("$relTable.$col", $operator, "%" . $value . "%");
                    }
                    return $q->{$method}("$relTable.$col", $operator, $value);
                });
            } else {
                if ($range) {
                    $query = $this->handleRangeQuery($value, $query, $this->tableName, $field);
                } elseif ($operator == 'like') {
                    $query = $query->{$method}($this->tableName . '.' . $field, $operator, "%" . $value . "%");
                } else {
                    $query = $query->{$method}($this->tableName . '.' . $field, $operator, $value);
                }
            }
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @param bool    $defaultOrder
     * @return Builder
     */
    private function orderQueryBy(Builder $query, bool $defaultOrder = true): Builder
    {
        $sortCol = request()->sort_col;
        $sortCol = $this->unsetEmptyParams($sortCol);
        $sortDir = request()->sort_dir ?? "DESC";

        if (!isset($sortCol) && $defaultOrder) {
            return $query->orderBy('created_at', 'DESC');
        }
        if (in_array($sortCol, array_keys($this->customOrders))) {
            $query = $this->customOrders[$sortCol]($query, $sortDir);
        } elseif (str_contains($sortCol, '.')) {
            [$relationName, $relatedColumn] = explode('.', $sortCol);

            if (method_exists($this->model, $relationName)) {
                $relationMethod = $this->model->{$relationName}();

                if ($relationMethod instanceof BelongsTo) {
                    $foreignKey = $relationMethod->getForeignKeyName();
                    $relatedTable = Str::plural(Str::snake($relationName));

                    $query->orderBy(function (QueryBuilder $q) use ($foreignKey, $relatedColumn, $relatedTable) {
                        $currentTable = $this->model->getTable();
                        return $q->from($relatedTable)
                            ->whereRaw("`$relatedTable`.id = `$currentTable`.$foreignKey")
                            ->select($relatedColumn);
                    }, $sortDir);
                }

            }
        } else {
            if (in_array($sortCol, $this->modelTableColumns)) {
                $query->orderBy($this->tableName . '.' . $sortCol, $sortDir);
            }
        }

        return $query;
    }

    /**
     * @param array $relationships
     * @param array $countable
     * @param int   $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function all_with_pagination(array $relationships = [], array $countable = [], int $per_page = 10): ?array
    {
        if ($this->perPage == "all") {
            $all = $this->globalQuery($relationships)->withCount($countable)->get();
        } else {
            $all = $this->globalQuery($relationships)->withCount($countable)->paginate($this->perPage);
        }
        if (count($all) > 0) {
            $pagination_data = $this->formatPaginateData($all);
            return ['data' => $all->items(), 'pagination_data' => $pagination_data];
        }
        return null;
    }

    /**
     * @param LengthAwarePaginator<T> $data
     * @return array
     */
    #[ArrayShape(['currentPage' => "mixed", 'from' => "mixed", 'to' => "mixed", 'total' => "mixed", 'per_page' => "mixed", 'total_pages' => "float", 'isFirst' => "bool", 'isLast' => "bool"])]
    public function formatPaginateData(LengthAwarePaginator $data): array
    {
        return [
            'currentPage' => $data->currentPage(),
            'total'       => $data->total(),
            'per_page'    => $data->perPage(),
            'total_pages' => $data->lastPage(),
            'isFirst'     => $data->onFirstPage(),
            'isLast'      => $data->onLastPage(),
        ];
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function create(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $receivedData = $data;
        $colNames = $this->fileColName($data);

        if (count($colNames)) {
            foreach ($colNames as $colName) {
                unset($receivedData[$colName]);
            }
        }

        $receivedData = $this->unsetNullable($receivedData);

        /** @var T $result */
        $result = $this->model->create($receivedData);

        if (count($colNames)) {
            $this->handleFiles($result, $data, $colNames);
        }

        $result->refresh();

        return $result->load($relationships)->loadCount($countable);
    }

    /**
     * @param        $data
     * @return array
     */
    private function fileColName($data): array
    {
        $keys = [];
        foreach ($data as $key => $value) {
            if (in_array($key, array_keys($this->fileColumnsName))) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * @param Model $object
     * @param array $data
     * @param array $fileKeys
     * @return void
     */
    public function handleFiles(Model $object, array $data, array $fileKeys): void
    {
        foreach ($fileKeys as $fileKey) {
            if ($this->fileColumnsName[$fileKey]['type'] == MediaTypeEnum::MULTIPLE->value) {

                foreach ($data[$fileKey] as $file) {
                    $object->addMedia($file)
                        ->toMediaCollection($this->fileColumnsName[$fileKey]['collection'] ?? 'default');
                }

            } elseif ($this->fileColumnsName[$fileKey]['type'] == MediaTypeEnum::SINGLE->value || $this->fileColumnsName[$fileKey]['type'] == null) {

                $oldMedia = $object->getMedia();

                if (count($oldMedia) and isset($data[$fileKey])) {
                    foreach ($oldMedia as $media) {
                        $media->delete();
                    }
                }

                if (isset($data[$fileKey])) {
                    $object->addMedia($data[$fileKey])
                        ->toMediaCollection($this->fileColumnsName[$fileKey]['collection'] ?? 'default');
                }
            }
        }
    }

    /**
     * @param            $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        $result = $this->model->where('id', '=', $id)->first();

        if ($result) {
            $result->delete();
            return true;
        }

        return null;
    }

    /**
     * @param       $id
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function find($id, array $relationships = [], array $countable = []): ?Model
    {
        $result = $this->model->with($relationships)->withCount($countable)->find($id);

        if ($result) {
            return $result;
        }

        return null;
    }

    /**
     * @param array   $data
     * @param T|mixed $id
     * @param array   $relationships
     * @param array   $countable
     * @return T|null
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $receivedData = $data;

        if ($id instanceof Model) {
            $item = $id;
        } else {
            $item = $this->model->where('id', '=', $id)->first();
        }

        if ($item) {
            $colNames = $this->fileColName($data);

            if (count($colNames)) {
                foreach ($colNames as $colName) {
                    unset($receivedData[$colName]);
                }
            }

            $receivedData = $this->unsetNullable($receivedData);

            $item->fill($receivedData);
            $item->save();

            if (count($colNames)) {
                $this->handleFiles($item, $data, $colNames);
            }

            return $item->load($relationships)->load($countable);
        }

        return null;
    }

    /**
     * @param array|null $ids
     * @return BinaryFileResponse
     */
    public function export(array $ids = null): BinaryFileResponse
    {
        if ($ids) {
            $collection = $this->globalQuery()->get();
        } else {
            $collection = $this->globalQuery()->whereIn('id', $ids)->get();
        }

        $requestedColumns = request("columns") ?? null;
        return Excel::download(
            new BaseExporter($collection, $this->model, $requestedColumns),
            $this->model->getTable() . ".xlsx",
        );
    }

    /**
     * @return BinaryFileResponse
     */
    public function getImportExample(): BinaryFileResponse
    {
        return Excel::download(
            new BaseExporter(collect(), $this->model, null, true),
            $this->model->getTable() . '-example.xlsx'
        );
    }

    /**
     * @return void
     */
    public function import(): void
    {
        Excel::import(new BaseImporter($this->model), request()->file('excel_file'));
    }

    protected function unsetEmptyParams(string|array|null $param = null): string|array|null
    {
        if (!$param) {
            return null;
        }
        if (is_array($param)) {
            foreach ($param as $value) {
                if (strlen(trim(preg_replace('/\s+/', '', $value))) != 0) {
                    return $param;
                }
            }
            return null;
        } elseif (strlen(trim(preg_replace('/\s+/', '', $param))) == 0) {
            return null;
        } else {
            return $param;
        }
    }

    /**
     * @param mixed   $value
     * @param Builder $query
     * @param string  $table
     * @param string  $column
     * @return Builder
     */
    private function handleRangeQuery(array $value, Builder $query, string $table, string $column): Builder
    {
        if (count($value) == 2) {
            if (!isset($value[0]) && isset($value[1])) {
                $query = $query->where("$table.$column", '<=', $value[1]);
            } elseif (isset($value[0]) && !isset($value[1])) {
                $query->where("$table.$column", '<=', $value[0]);
            } elseif (isset($value[0]) && isset($value[1])) {
                $query = $query->where("$table.$column", '>=', $value[0])
                    ->where("$table.$column", ">=", $value[1]);
            }
        } elseif (count($value) > 2) {
            $query->whereIn("$table.$column", array_values(array_filter($value)));
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @return array{data:Collection<T>|RegularCollection<T>|T[] , pagination_data:array}|null
     */
    public function paginateQuery(Builder $query): ?array
    {
        $data = $query->paginate($this->perPage ?? 10);
        if ($data->count()) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data),
            ];
        } else {
            return null;
        }
    }
}
