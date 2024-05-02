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
use Illuminate\Support\Collection as RegularCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T as Model
 * @implements IBaseRepository<T>
 */
abstract class BaseRepository implements IBaseRepository
{
    use FileHandler;

    /**
     * @var T
     */
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

    /**
     * @param T $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
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

        $this->modelTableColumns = $this->getTableColumns();
    }

    public function getTableColumns(): array
    {
        $table = $this->model->getTable();

        return Schema::getColumnListing($table);
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

    /**
     * @param array $relations
     * @param array $countable
     * @return Builder|T
     */
    public function globalQuery(array $relations = [], array $countable = []): Builder
    {
        $query = $this->model->with($relations)->withCount($countable);

        if (request()->method() == 'GET') {
            $query = $this->addSearch($query);
            $query = $this->filterFields($query);
            $query = $this->orderQueryBy($query);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder|T
     */
    private function addSearch(Builder $query): Builder
    {
        if (request()->has('search')) {
            $keyword = request()->search;
            if (count($this->searchableKeys) > 0) {
                foreach ($this->searchableKeys as $search_attribute) {
                    $query->orWhere("{$this->tableName}.{$search_attribute}", 'REGEXP', "(?i).*$keyword.*");
                }
            }

            if (count($this->relationSearchableKeys) > 0) {
                foreach ($this->relationSearchableKeys as $relation => $values) {
                    foreach ($values as $search_attribute) {
                        $query->orWhereRelation($relation, function ($q) use ($relation, $keyword, $search_attribute) {
                            $relSeq = explode('.', $relation);
                            $relTable = $relSeq[count($relSeq) - 1];
                            $relTable = Str::plural($relTable);
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

            if (!$value) {
                continue;
            }

            if ($relation) {
                $tables = explode('.', $relation);
                $relTable = Str::plural($tables[count($tables) - 2]);
                $col = $tables[count($tables) - 1];
                unset($tables[count($tables) - 1]);
                $relation = implode('.', $tables);

                $query = $query->whereRelation($relation, function (Builder $q) use ($col, $relTable, $relation, $range, $field, $method, $operator, $value) {

                    if ($range) {
                        return $q->whereBetween("$relTable.{$col}", $value);
                    }
                    if ($operator === "like") {
                        return $q->{$method}("$relTable.$col", $operator, "%" . $value . "%");
                    }
                    return $q->{$method}("$relTable.$col", $operator, $value);
                });
            } elseif ($callback && is_callable($callback)) {
                $query = call_user_func($callback, $query, $value);
            } else {
                if ($range) {
                    $query = $query->whereBetween($this->tableName . '.' . $field, $value);
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
     * @return Builder|T
     */
    private function orderQueryBy(Builder $query): Builder
    {
        $sortCol = request()->sort_col;
        $sortDir = request()->sort_dir;

        if (isset($sortCol)) {
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
                        }, $sortDir ?? 'asc');
                    }

                }
            } else {
                if (in_array($sortCol, $this->modelTableColumns)) {
                    $query->orderBy($this->tableName . '.' . $sortCol, $sortDir ?? 'asc');
                }
            }
        }

        return $query;
    }

    /**
     * @param array $relationships
     * @param array $countable
     * @param int $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function all_with_pagination(array $relationships = [], array $countable = [], int $per_page = 10): ?array
    {
        $per_page = request('per_page') ?? $per_page;
        $all = $this->globalQuery($relationships)->withCount($countable)->paginate($per_page);
        if (count($all) > 0) {
            $pagination_data = $this->formatPaginateData($all);
            return ['data' => $all, 'pagination_data' => $pagination_data];
        }
        return null;
    }

    /**
     * @param        $data
     * @return array
     */
    public function formatPaginateData($data): array
    {
        $paginated_arr = $data->toArray();

        return [
            'currentPage' => $paginated_arr['current_page'],
            'from' => $paginated_arr['from'],
            'to' => $paginated_arr['to'],
            'total' => $paginated_arr['total'],
            'per_page' => $paginated_arr['per_page'],
            'total_pages' => ceil($paginated_arr['total'] / $paginated_arr['per_page']),
            'isFirst' => $paginated_arr['current_page'] == 1,
            'isLast' => $paginated_arr['current_page'] == ceil($paginated_arr['total'] / $paginated_arr['per_page']),
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

                $oldMedia = $object->getMedia();

                if (count($oldMedia) and isset($data[$fileKey])) {
                    foreach ($oldMedia as $media) {
                        $media->delete();
                    }
                }

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
     * @param $id
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
     * @param array $data
     * @param T|mixed $id
     * @param array $relationships
     * @param array $countable
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
     * @param array $ids
     * @return BinaryFileResponse
     */
    public function export(array $ids = []): BinaryFileResponse
    {
        if (!count($ids)) {
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
}
