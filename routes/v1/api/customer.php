<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');

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
