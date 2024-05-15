<?php

namespace App\Services\BlockedItem;

use App\Models\BlockedItem;
use App\Services\Contracts\BaseService;
use App\Repositories\BlockedItemRepository;

/**
 * @implements IBlockedItemService<BlockedItem>
 * Class UserService
 */
class BlockedItemService extends BaseService implements IBlockedItemService
{
    /**
     * BlockedItemService constructor.
     * @param BlockedItemRepository $repository
     */
    public function __construct(BlockedItemRepository $repository)
    {
        parent::__construct($repository);
    }
}
