<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as RegularCollection;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T of Model
 */
interface IBaseService
{
    /**
     * @param            $id
     * @return bool|null
     */
    public function delete($id): ?bool;

    /**
     * @param        $data
     * @return array
     */
    #[ArrayShape(['currentPage' => 'int', 'from' => 'int', 'to' => 'int', 'total' => 'int', 'per_page' => 'int'])]
    public function formatPaginationData($data): array;

    /**
     * @param array $relations
     * @param array $countable
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function index(array $relations = [], array $countable = []): RegularCollection|Collection|array;

    /**
     * @param array $relations
     * @param array $countable
     * @param int $per_page
     * @return array{data:Collection<T>|array<T>|RegularCollection<T> , pagination_data:array}|null
     */
    public function indexWithPagination(array $relations = [], array $countable = [], int $per_page = 10): ?array;

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Model;

    /**
     * @param array $data
     * @param T|int $id
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model;

    /**
     * @param $id
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function view($id, array $relationships = [], array $countable = []): ?Model;

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
