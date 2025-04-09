<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\SecretaryAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\SecretaryAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\SecretaryAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\SecretaryAuthController::class, 'userDetails'])->name('user.details');
Route::post('fcm/store-token', [v1\SecretaryAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [v1\SecretaryAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');
