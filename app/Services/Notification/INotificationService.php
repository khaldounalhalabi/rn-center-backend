<?php

namespace App\Services\Notification;

use App\Services\Contracts\IBaseService;

/**
 * Interface IWishlistService
 */
interface INotificationService extends IBaseService
{
    public function markNotificationAsRead($id): bool;

    public function markLatestFiveAsRead(): bool;
}
