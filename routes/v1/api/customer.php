<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;


//add-your-routes-here

Route::post('/customer/refresh', [v1\CustomerAuthController::class, 'refresh'])->middleware('auth:api')->name("customer.refresh-token");
Route::post('/customer/logout', [v1\CustomerAuthController::class, 'logout'])->middleware('auth:api')->name("customer.logout");
Route::post('/customer/update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::get('/customer/me', [v1\CustomerAuthController::class, 'userDetails'])->name('customer.me');

