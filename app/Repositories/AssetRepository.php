<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Asset>
 */
class AssetRepository extends BaseRepository
{
    protected string $modelClass = Asset::class;

    public function all_with_pagination(array $relationships = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relationships, $countable)
                ->withSum('assignedUserAssets', 'quantity'),
        );
    }
}
