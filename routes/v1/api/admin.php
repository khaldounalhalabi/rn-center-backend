<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\v1;

use Illuminate\Support\Facades\Route;

//add-your-routes-here
Route::post('/admin/register', [v1\AdminAuthController::class, 'register'])->name("admin.register");
Route::post('/admin/login', [v1\AdminAuthController::class, 'login'])->name("admin.login");
Route::post('/admin/refresh', [v1\AdminAuthController::class, 'refresh'])->middleware('auth:api')->name("admin.refresh-token");
Route::post('/admin/password-reset-request', [v1\AdminAuthController::class, 'passwordResetRequest'])->name("admin.reset-password-request");
Route::post('/admin/check-reset-password-code', [v1\AdminAuthController::class, 'checkPasswordResetCode'])->name("admin.check-reset-password-code");
Route::post('/admin/reset-password', [v1\AdminAuthController::class, 'passwordReset'])->name("admin.password-reset");
Route::post('/admin/logout', [v1\AdminAuthController::class, 'logout'])->middleware('auth:api')->name("admin.logout");
Route::post('/admin/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');

Route::apiResource("/customers", v1\CustomerController::class)->names("api.admin.customers") ;
