<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;


//add-your-routes-here
Route::post('/customer/register', [v1\CustomerAuthController::class, 'register'])->name("customer.register");
Route::post('/customer/login', [v1\CustomerAuthController::class, 'login'])->name("customer.login");
Route::post('/customer/refresh', [v1\CustomerAuthController::class, 'refresh'])->middleware('auth:api')->name("customer.refresh-token");
Route::post('/customer/password-reset-request', [v1\CustomerAuthController::class, 'passwordResetRequest'])->name("customer.reset-password-request");
Route::post('/customer/check-reset-password-code', [v1\CustomerAuthController::class, 'checkPasswordResetCode'])->name("customer.check-reset-password-code");
Route::post('/customer/reset-password', [v1\CustomerAuthController::class, 'passwordReset'])->name("customer.password-reset");
Route::post('/customer/logout', [v1\CustomerAuthController::class, 'logout'])->middleware('auth:api')->name("customer.logout");
Route::post('/customer/update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::get('/customer/me', [v1\CustomerAuthController::class, 'userDetails'])->name('customer.me');

