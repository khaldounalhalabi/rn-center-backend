<?php


use App\Http\Controllers\API\v1\BaseAuthController;

Route::post('fcm/store-token', [BaseAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [BaseAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('/me', [BaseAuthController::class, 'userDetails'])->name('me');
