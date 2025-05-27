<?php

namespace App\Modules\SharedModule;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as DBBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class Common
{
    /**
     * @param Builder|DBBuilder|Model|Relation $query
     * @param int                              $limit
     * @return array{data:mixed , pagination_data:array}|null
     */
    public static function paginateQueryMethod(Builder|DBBuilder|Model|Relation $query, int $limit): ?array
    {
        $data = $query->paginate($limit);
        if (!$data->count()) {
            return null;
        }
        return [
            'data' => $data->items(),
            'pagination_data' => self::formatPaginateData($data)
        ];
    }

    public static function formatPaginateData(LengthAwarePaginator $data): array
    {
        return [
            'currentPage' => $data->currentPage(),
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'total_pages' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'is_first' => $data->onFirstPage(),
            'is_last' => $data->onLastPage(),
        ];
    }
}
