<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as RegularCollection;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Interface IBaseRepository
 */
interface IBaseRepository
{
    /**
     * @template T of Model<T>
     * @param array $relationships
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function all(array $relationships = []): Collection|array|RegularCollection;

    /**
     * @template T of Model<T>
     * @param array $relationships
     * @param int $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function all_with_pagination(array $relationships = [], int $per_page = 10): ?array;

    /**
     * @template T of Model<T>
     * @param array $data
     * @param array $relationships
     * @return T|null
     */
    public function create(array $data, array $relationships = []): mixed;

    /**
     * @param $id
     * @return bool|null
     */
    public function delete($id): ?bool;

    /**
     * @template T of Model<T>
     * @return T|null
     */
    public function find($id, array $relationships = []): ?Model;

    /**
     * @param $data
     * @return array
     */
    #[ArrayShape(['currentPage' => 'int', 'from' => 'int', 'to' => 'int', 'total' => 'int', 'per_page' => 'int'])]
    public function formatPaginateData($data): array;

    /**
     * @template T of Model<T>
     * @param array $data
     * @param $id
     * @param array $relationships
     * @return T
     */
    public function update(array $data, $id, array $relationships = []): mixed;
}
