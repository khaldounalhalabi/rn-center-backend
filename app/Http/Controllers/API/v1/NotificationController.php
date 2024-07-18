<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;

class NotificationController extends ApiController
{
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = NotificationService::make();
    }

    public function getUserNotification()
    {
        $notifications = $this->notificationService->getUserNotifications();
        if ($notifications) {
            return $this->apiResponse(NotificationResource::collection($notifications['data']), self::STATUS_OK, __('site.get_successfully'), $notifications['pagination_data']);
        }
        return $this->noData();
    }

    public function markAsRead($notificationId)
    {
        $item = $this->notificationService->update([
            'read_at' => now()
        ], $notificationId);

        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.success'));
        }
        return $this->noData(false);
    }

    public function unreadCount()
    {
        return $this->apiResponse(
            auth()->user()?->unreadNotifications()->count(),
            self::STATUS_OK,
            __('site.success')
        );
    }
}
