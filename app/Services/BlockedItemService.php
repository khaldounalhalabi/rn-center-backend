<?php

namespace App\Services;

use App\Models\BlockedItem;
use App\Repositories\BlockedItemRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<BlockedItem , BlockedItemRepository>
 * @property BlockedItemRepository $repository
 */
class BlockedItemService extends BaseService
{
    use Makable;

    protected string $repositoryClass = BlockedItemRepository::class;
}
