<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\v1;

use Illuminate\Support\Facades\Route;


//add-your-routes-here
Route::post('/doctor/register', [v1\DoctorAuthController::class, 'register'])->name("doctor.register");
Route::post('/doctor/login', [v1\DoctorAuthController::class, 'login'])->name("doctor.login");
Route::post('/doctor/refresh', [v1\DoctorAuthController::class, 'refresh'])->middleware('auth:api')->name("doctor.refresh-token");
Route::post('/doctor/password-reset-request', [v1\DoctorAuthController::class, 'passwordResetRequest'])->name("doctor.reset-password-request");
Route::post('/doctor/check-reset-password-code', [v1\DoctorAuthController::class, 'checkPasswordResetCode'])->name("doctor.check-reset-password-code");
Route::post('/doctor/reset-password', [v1\DoctorAuthController::class, 'passwordReset'])->name("doctor.password-reset");
Route::post('/doctor/logout', [v1\DoctorAuthController::class, 'logout'])->middleware('auth:api')->name("doctor.logout");
Route::post('/doctor/update-user-data' , [v1\DoctorAuthController::class, 'updateUserDetails'])->name('update-user-data');

