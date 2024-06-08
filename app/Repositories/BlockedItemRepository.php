<?php

namespace App\Repositories;

use App\Models\BlockedItem;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<BlockedItem>
 */
class BlockedItemRepository extends BaseRepository
{
    protected string $modelClass = BlockedItem::class;
}
