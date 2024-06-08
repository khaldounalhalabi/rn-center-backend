<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;


//add-your-routes-here

Route::post('refresh', [v1\DoctorAuthController::class, 'refresh'])->middleware('auth:api')->name("refresh-token");
Route::post('logout', [v1\DoctorAuthController::class, 'logout'])->middleware('auth:api')->name("logout");
Route::post('update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name('update-user-data');

Route::put('/clinic/update', [v1\ClinicController::class, 'updateDoctorClinic'])->name('clinic.update');
