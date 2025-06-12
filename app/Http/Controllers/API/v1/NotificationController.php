<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\NotificationResource;
use App\Modules\Notification\App\Services\NotificationService;

class NotificationController extends ApiController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = NotificationService::make();
    }

    public function myNotifications()
    {
        $data = $this->service->myNotifications();
        if ($data) {
            return $this->apiResponse(
                NotificationResource::collection($data['data']),
                self::STATUS_OK,
                __('site.get_successfully'),
                $data['pagination_data'],
            );
        }

        return $this->noData();
    }

    public function markAsRead($notificationId)
    {
        $result = $this->service->markNotificationAsRead($notificationId);
        if ($result) {
            return $this->apiResponse(
                $result,
                self::STATUS_OK,
                __('site.success'),
            );
        }

        return $this->noData();
    }

    public function unreadCount()
    {
        return $this->apiResponse(
            [
                'unread_count' => $this->service->unreadCount()
            ],
            self::STATUS_OK,
            __('site.success'),
        );
    }

    public function markAllAsRead()
    {
        return $this->apiResponse(
            [
                'marked' => $this->service->markAllAsRead()
            ],
            self::STATUS_OK,
            __('site.success'),
        );
    }
}
