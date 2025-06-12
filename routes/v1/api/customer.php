<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');

Route::get('/notifications', [v1\NotificationController::class, 'myNotifications'])->name('notifications.index');
Route::get('/notifications/{notificationId}/read', [v1\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/notifications/read-all', [v1\NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
Route::get('/notifications/unread-count', [v1\NotificationController::class, 'unreadCount'])->name('notifications.unread.count');
