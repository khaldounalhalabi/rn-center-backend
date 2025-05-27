<?php

namespace App\Modules\SharedModule\Traits;

use App\Modules\SharedModule\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as DBBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

trait Make
{
    private static $instance;

    public static function make(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (!(self::$instance instanceof static)) {
            self::$instance = new static();
        }

        self::$instance->init();
        return self::$instance;
    }

    public function init(): void
    {

    }

    /**
     * @param Builder|DBBuilder|Model|Relation $query
     * @return array{data:mixed , pagination_data:array}|null
     */
    protected function paginate(Builder|DBBuilder|Model|Relation $query): array|null
    {
        return Common::paginateQueryMethod($query, $this->limit);
    }

    protected function formatPaginateData(LengthAwarePaginator $data): array
    {
        return Common::formatPaginateData($data);
    }
}
