<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');
Route::post('/fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('/fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('/notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');


Route::get('/clinics/{clinicId}/toggle-follow', [v1\FollowerController::class, 'toggleFollow'])->name('follower.follow.toggle');
Route::get('/followed', [v1\FollowerController::class, 'getFollowedClinics'])->name('followed');

Route::apiResource('reviews', v1\ReviewController::class)
    ->except(['show', 'index'])->names('reviews');

Route::get('/appointments/today', [v1\AppointmentController::class, 'getCustomerTodayAppointments'])
    ->name('appointments.today');
Route::put('/appointments/{appointmentId}/change-date', [v1\AppointmentController::class, 'updateAppointmentDate'])
    ->name('appointments.change.date');
Route::apiResource('appointments', v1\AppointmentController::class)
    ->except(['destroy', 'update'])
    ->names('appointments');

Route::get('/hospitals' , [v1\HospitalController::class , 'getByUserCity'])->name('hospitals');
