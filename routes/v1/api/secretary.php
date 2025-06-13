<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\SecretaryAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\SecretaryAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\SecretaryAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\SecretaryAuthController::class, 'userDetails'])->name('user.details');

Route::get('/notifications', [v1\NotificationController::class, 'myNotifications'])->name('notifications.index');
Route::get('/notifications/{notificationId}/read', [v1\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/notifications/read-all', [v1\NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
Route::get('/notifications/unread-count', [v1\NotificationController::class, 'unreadCount'])->name('notifications.unread.count');

Route::get('/holidays/active', [v1\HolidayController::class, 'activeHolidays'])->name('holidays.active');
Route::get('holidays', [v1\HolidayController::class, 'index'])->name('holidays.index');
