<?php

namespace App\Services;

use App\Enums\AssetStatusEnum;
use App\Models\Asset;
use App\Repositories\AssetRepository;
use App\Repositories\UserAssetRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Asset>
 * @property AssetRepository $repository
 */
class AssetService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AssetRepository::class;

    public function checkin(array $data, array $relations = [], array $countable = []): ?Asset
    {
        $asset = $this->repository->find($data['asset_id']);

        if (!$asset->canCheckin()) {
            return null;
        }

        if ($data['quantity'] > $asset->quantity) {
            return null;
        }

        $userAsset = UserAssetRepository::make()->getAssignedByAssetAndUser($data['asset_id'], $data['user_id']);

        if ($userAsset) {
            UserAssetRepository::make()->update([
                'checkin_date' => now(),
                'quantity' => $data['quantity'] + $userAsset->quantity ?? 0,
                'checkin_condition' => $data['checkin_condition'] ?? null,
                'expected_return_date' => $data['expected_return_date'] ?? null,
            ], $userAsset);
        } else {
            UserAssetRepository::make()->create([
                'asset_id' => $data['asset_id'],
                'user_id' => $data['user_id'],
                'status' => AssetStatusEnum::CHECKIN->value,
                'quantity' => $data['quantity'],
                'checkin_condition' => $data['checkin_condition'] ?? null,
                'checkin_date' => now(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
            ]);
        }

        return $this->repository->update([
            'quantity' => $asset->quantity - $data['quantity'],
        ], $asset, $relations, $countable);
    }

    public function checkout(array $data, array $relations = [], array $countable = [])
    {
        $asset = $this->repository->find($data['asset_id']);

        if (!$asset->canCheckout()) {
            return null;
        }

        $userAsset = UserAssetRepository::make()->getAssignedByAssetAndUser($data['asset_id'], $data['user_id']);
        if (!$userAsset) {
            return null;
        }

        if ($data['quantity'] > $userAsset->quantity) {
            return null;
        }

        if ($data['quantity'] < $userAsset->quantity) {
            UserAssetRepository::make()->update([
                'quantity' => $userAsset->quantity - $data['quantity'],
            ], $userAsset);
        } else {
            UserAssetRepository::make()->update([
                'checkout_date' => now(),
                'checkin_condition' => $data['checkin_condition'] ?? null,
                'status' => AssetStatusEnum::CHECKOUT->value,
            ], $userAsset);
        }

        return $this->repository->update([
            'quantity' => $asset->quantity + $data['quantity'],
        ], $asset, $relations, $countable);
    }
}
