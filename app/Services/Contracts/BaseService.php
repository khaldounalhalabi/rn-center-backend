<?php

namespace App\Services\Contracts;

use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as RegularCollection;
use JetBrains\PhpStorm\ArrayShape;

/**
 *
 */
abstract class BaseService implements IBaseService
{
    /**
     * @var IBaseRepository
     */
    protected IBaseRepository $repository;

    /**
     * @param IBaseRepository $repository
     */
    public function __construct(IBaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        return $this->repository->delete($id);
    }

    /**
     * @param $data
     * @return array
     */
    #[ArrayShape(['currentPage' => 'int', 'from' => 'int', 'to' => 'int', 'total' => 'int', 'per_page' => 'int'])]
    public function formatPaginationData($data): array
    {
        return $this->repository->formatPaginateData($data);
    }

    /**
     * @template T of Model<T>
     * @param array $relations
     * @return Collection<T>|RegularCollection<T>|array
     */
    public function index(array $relations = []): RegularCollection|Collection|array
    {
        return $this->repository->all($relations);
    }

    /**
     * @template T of Model<T>
     * @param array $relations
     * @param int $per_page
     * @return array{data:Collection<T>|array|RegularCollection<T> , pagination_data:array}|null
     */
    public function indexWithPagination(array $relations = [], int $per_page = 10): ?array
    {
        return $this->repository->all_with_pagination($relations, $per_page);
    }

    /**
     * @template T of Model<T>
     * @param array $data
     * @param array $relationships
     * @return T|null
     */
    public function store(array $data, array $relationships = []): ?Model
    {
        return $this->repository->create($data, $relationships);
    }

    /**
     * @template T of Model<T>
     * @param array $data
     * @param $id
     * @param array $relationships
     * @return T
     */
    public function update(array $data, $id, array $relationships = []): ?Model
    {
        return $this->repository->update($data, $id, $relationships);
    }

    /**
     * @template T of Model<T>
     * @return T|null
     */
    public function view($id, array $relationships = []): ?Model
    {
        return $this->repository->find($id, $relationships);
    }
}
