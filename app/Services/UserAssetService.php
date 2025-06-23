<?php

namespace App\Services;

use App\Models\UserAsset;
use App\Repositories\UserAssetRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<UserAsset>
 * @property UserAssetRepository $repository
 */
class UserAssetService extends BaseService
{
    use Makable;

    protected string $repositoryClass = UserAssetRepository::class;

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByUser($userId, $relations, $countable);
    }

    public function getByAsset(int $assetId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getByAsset($assetId, $relations, $countable);
    }
}
