<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\DoctorAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\DoctorAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\DoctorAuthController::class, 'userDetails'])->name('user.details');
Route::post('fcm/store-token', [v1\DoctorAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [v1\DoctorAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::prefix('appointments')
    ->name('appointments.')
    ->group(function () {
        Route::get('/', [v1\AppointmentController::class, 'index'])->name('index');
        Route::get('/{appointmentId}', [v1\AppointmentController::class, 'show'])->name('show');
    });
