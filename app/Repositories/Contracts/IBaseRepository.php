<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as RegularCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T as Model
 */
interface IBaseRepository
{
    /**
     * @param array $relationships
     * @param array $countable
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function all(array $relationships = [], array $countable = []): Collection|array|RegularCollection;

    /**
     * @param array $relationships
     * @param array $countable
     * @param int $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function all_with_pagination(array $relationships = [], array $countable = [], int $per_page = 10): ?array;

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Model|null
     */
    public function create(array $data, array $relationships = [], array $countable = []): ?Model;

    /**
     * @param            $id
     * @return bool|null
     */
    public function delete($id): ?bool;

    /**
     * @param $id
     * @param array $relationships
     * @param array $countable
     * @return Model|null
     */
    public function find($id, array $relationships = [], array $countable = []): ?Model;

    /**
     * @param        $data
     * @return array
     */
    public function formatPaginateData($data): array;

    /**
     * @param array $data
     * @param T|int $id
     * @param array $relationships
     * @param array $countable
     * @return T
     */
    public function update(array $data, Model|int $id, array $relationships = [], array $countable = []): mixed;

    /**
     * @param array $ids
     * @return BinaryFileResponse
     */
    public function export(array $ids = []): BinaryFileResponse;

    /**
     * @return BinaryFileResponse
     */
    public function getImportExample(): BinaryFileResponse;

    /**
     * @return void
     */
    public function import(): void;
}
