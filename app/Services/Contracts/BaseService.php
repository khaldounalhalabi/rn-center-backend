<?php

namespace App\Services\Contracts;

use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as RegularCollection;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @template T of Model
 */
abstract class BaseService
{
    /**
     * @var class-string<BaseRepository<T>>
     */
    protected string $repositoryClass = BaseRepository::class;

    protected BaseRepository $repository;

    protected function __construct()
    {
        $this->repository = new $this->repositoryClass();
    }

    /**
     * @param            $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        return $this->repository->delete($id);
    }

    /**
     * @param LengthAwarePaginator<T> $data
     * @return array
     */
    #[ArrayShape(['currentPage' => "mixed", 'from' => "mixed", 'to' => "mixed", 'total' => "mixed", 'per_page' => "mixed", 'total_pages' => "float", 'isFirst' => "bool", 'isLast' => "bool"])]
    public function formatPaginationData(LengthAwarePaginator $data): array
    {
        return $this->repository->formatPaginateData($data);
    }

    /**
     * @param array $relations
     * @param array $countable
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function index(array $relations = [], array $countable = []): RegularCollection|Collection|array
    {
        return $this->repository->all($relations, $countable);
    }

    /**
     * @param array $relations
     * @param array $countable
     * @param int   $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function indexWithPagination(array $relations = [], array $countable = [], int $per_page = 10): ?array
    {
        return $this->repository->all_with_pagination($relations, $countable);
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        return $this->repository->create($data, $relationships, $countable);
    }

    /**
     * @param array  $data
     * @param        $id
     * @param array  $relationships
     * @param array  $countable
     * @return T|null
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        return $this->repository->update($data, $id, $relationships, $countable);
    }

    /**
     * @param       $id
     * @param array $relationships
     * @param array $countable
     * @return T|null
     */
    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        return $this->repository->find($id, $relationships, $countable);
    }

    /**
     * @param array $ids
     * @return BinaryFileResponse
     */
    public function export(array $ids = null): BinaryFileResponse
    {
        return $this->repository->export($ids);
    }

    /**
     * @return BinaryFileResponse
     */
    public function getImportExample(): BinaryFileResponse
    {
        return $this->repository->getImportExample();
    }

    /**
     * @return void
     */
    public function import(): void
    {
        $this->repository->import();
    }
}
