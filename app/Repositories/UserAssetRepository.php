<?php

namespace App\Repositories;

use App\Enums\AssetStatusEnum;
use App\Models\UserAsset;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<UserAsset>
 */
class UserAssetRepository extends BaseRepository
{
    protected string $modelClass = UserAsset::class;

    public function getAssignedByAssetAndUser(int $assetId, int $userId, array $relations = [], array $countable = []): ?UserAsset
    {
        return $this->globalQuery($relations, $countable)
            ->where('asset_id', $assetId)
            ->where('user_id', $userId)
            ->where('status', AssetStatusEnum::CHECKIN->value)
            ->first();
    }

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('user_id', $userId)
        );
    }

    public function getByAsset($assetId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginateQuery(
            $this->globalQuery($relations, $countable)
                ->where('asset_id', $assetId)
        );
    }
}
