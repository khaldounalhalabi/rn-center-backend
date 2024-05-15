<?php

namespace App\Repositories;

use App\Models\BlockedItem;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<BlockedItem>
 */
class BlockedItemRepository extends BaseRepository
{
    public function __construct(BlockedItem $blockedItem)
    {
        parent::__construct($blockedItem);
    }
}
