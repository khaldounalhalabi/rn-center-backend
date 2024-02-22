<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\v1;

use Illuminate\Support\Facades\Route;


//add-your-routes-here

Route::post('/doctor/refresh', [v1\DoctorAuthController::class, 'refresh'])->middleware('auth:api')->name("doctor.refresh-token");
Route::post('/doctor/logout', [v1\DoctorAuthController::class, 'logout'])->middleware('auth:api')->name("doctor.logout");
Route::post('/doctor/update-user-data' , [v1\DoctorAuthController::class, 'updateUserDetails'])->name('update-user-data');

