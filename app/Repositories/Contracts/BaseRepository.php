<?php

namespace App\Repositories\Contracts;

use App\Enums\MediaTypeEnum;
use App\Excel\BaseExporter;
use App\Excel\BaseImporter;
use App\Traits\FileHandler;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection as RegularCollection;
use Illuminate\Support\Str;
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
    protected $perPage;
    private Filesystem $fileSystem;
    private array $filterKeys = [];
    private array $fileColumnsName = [];
    private array $modelTableColumns;
    private array $orderableKeys = [];
    private array $relationSearchableKeys = [];
    private array $searchableKeys = [];
    private array $customOrders = [];
    private string $tableName;

    /**
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

        $this->modelTableColumns = $this->model->getFillable();
        $this->perPage = request('per_page', 10);
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
        return $this->globalQuery($relationships, $countable)->get();
    }

    /**
     * @param array $relations
     * @param array $countable
     * @param bool  $defaultOrder
     * @return Builder|T
     */
    public function globalQuery(array $relations = [], array $countable = [], bool $defaultOrder = true): Builder|Model
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
            if (!$value) {
                continue;
            }

            if ($range) {
                $value = array_values($value);
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
                        return $this->handleRangeQuery($value, $q, $relTable, $col, $method);
                    }
                    if ($operator === "like") {
                        return $q->{$method}("$relTable.$col", $operator, "%" . $value . "%");
                    }
                    return $q->{$method}("$relTable.$col", $operator, $value);
                });
            } else {
                if ($range) {
                    $query = $this->handleRangeQuery($value, $query, $this->tableName, $field, $method);
                } elseif ($operator == 'like') {
                    $query = $query->{$method}($this->tableName . '.' . $field, $operator, "%" . $value . "%");
                } else {
                    $query = $query->{$method}($this->tableName . '.' . $field, $operator, $value);
                }
            }
        }
        return $query;
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
     * @param string  $method
     * @return Builder
     */
    private function handleRangeQuery(array $value, Builder $query, string $table, string $column, string $method): Builder
    {
        if ($method == 'whereIn') {
            $query->whereIn("$table.$column", array_values(array_filter($value)));
            return $query;
        }
        if (count($value) == 2) {
            if (!isset($value[0]) && isset($value[1])) {
                $query = $query->$method("$table.$column", '<=', $value[1]);
            } elseif (isset($value[0]) && !isset($value[1])) {
                $query->$method("$table.$column", '>=', $value[0]);
            } elseif (isset($value[0]) && isset($value[1])) {
                $query = $query->$method("$table.$column", '>=', $value[0])
                    ->$method("$table.$column", '<=', $value[1]);
            }
        } elseif (count($value) > 2) {
            $query->whereIn("$table.$column", array_values(array_filter($value)));
        }
        return $query;
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
                    $query->orWhere("$this->tableName.{$search_attribute}", 'LIKE', "%$keyword%");
                }
            }

            if (count($this->relationSearchableKeys) > 0) {
                foreach ($this->relationSearchableKeys as $relation => $values) {
                    foreach ($values as $search_attribute) {
                        $query->orWhereRelation($relation, function (Builder $q) use ($relation, $keyword, $search_attribute) {
                            $relTable = $q->getModel()->getTable();
                            $q->where("{$relTable}.{$search_attribute}", 'LIKE', "%$keyword%");
                        });
                    }
                }
            }
            $query->orWhere($this->tableName . '.id', $keyword);
        }

        return $query;
    }

    /**
     * @param Builder    $query
     * @param bool       $defaultOrder
     * @param array|null $defaultCols
     * @return Builder
     */
    protected function orderQueryBy(Builder $query, bool $defaultOrder = true, ?array $defaultCols = null): Builder
    {
        $sortCol = request('sort_col');
        $sortCol = $this->unsetEmptyParams($sortCol);
        $sortDir = request('sort_dir') ?? "DESC";

        if (!isset($sortCol) && $defaultOrder) {
            if ($defaultCols) {
                foreach ($defaultCols as $col => $dir) {
                    $query = $query->orderBy($col, $dir);
                }
                return $query;
            } else {
                return $query->orderBy('created_at', 'DESC');
            }
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
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function all_with_pagination(array $relationships = [], array $countable = []): ?array
    {
        if ($this->perPage == "all") {
            $all = $this->globalQuery($relationships)->withCount($countable)->get();
        } else {
            $all = $this->globalQuery($relationships)->withCount($countable)->simplePaginate($this->perPage);
        }
        if (count($all) > 0) {
            $pagination_data = $this->formatPaginateData($all);
            return ['data' => $all->items(), 'pagination_data' => $pagination_data];
        }
        return null;
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
    public function update(array $data, mixed $id, array $relationships = [], array $countable = []): ?Model
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

            $item->fill($receivedData);
            $item->save();

            if (count($colNames)) {
                $this->handleFiles($item, $data, $colNames);
            }

            return $item->load($relationships)->loadCount($countable);
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

    /**
     * @param Builder $query
     * @return array{data:Collection<T>|RegularCollection<T>|T[] , pagination_data:array}|null
     */
    public function paginateQuery(Builder $query): ?array
    {
        $data = $query->simplePaginate($this->perPage ?? 10);
        if ($data->count()) {
            return [
                'data' => $data,
                'pagination_data' => $this->formatPaginateData($data),
            ];
        } else {
            return null;
        }
    }

    protected function paginate(Builder $query, int $perPage = 10): ?array
    {
        $perPage = request('per_page', $perPage);
        $data = $query->simplePaginate($perPage);
        if ($data->count()) {
            return [
                'data' => $data,
                'pagination_data' => $this->formatPaginateData($data),
            ];
        }

        return null;
    }

    /**
     * @param Paginator $data
     * @return array
     */
    public function formatPaginateData(Paginator $data): array
    {
        return [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'is_first' => $data->onFirstPage(),
            'is_last' => $data->onLastPage(),
            'has_more' => $data->hasMorePages(),
        ];
    }
}
