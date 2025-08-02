<?php


use App\Http\Controllers\API\v1\BaseAuthController;

Route::post('fcm/store-token', [BaseAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');

Route::get('/me', [BaseAuthController::class, 'userDetails'])->name('me');
