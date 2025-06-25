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

    public function getAssignedByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getAssignedByUser($userId, $relations, $countable);
    }

    public function getAssignedByAsset(int $assetId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->getAssignedByAsset($assetId, $relations, $countable);
    }
}
