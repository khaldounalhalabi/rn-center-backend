<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

//add-your-routes-here
Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->middleware('auth:api')->name("admin.refresh-token");
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->middleware('auth:api')->name("admin.logout");
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');

Route::apiResource("/customers", v1\CustomerController::class)->names("api.admin.customers");
Route::apiResource("/clinics", v1\ClinicController::class)->names("api.admin.clinics");
Route::apiResource("/schedules", v1\ScheduleController::class)->names("api.admin.schedules") ;
