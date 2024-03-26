<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

//add-your-routes-here
Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name("admin.refresh-token");
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name("admin.logout");
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::apiResource("/clinics", v1\ClinicController::class)->names("api.admin.clinics");

Route::get('/clinics/{clinicId}/schedules', [v1\ScheduleController::class, 'clinicSchedules'])->name('clinics.schedules');
Route::apiResource("/schedules", v1\ScheduleController::class)
    ->except(['index', 'show'])
    ->names("api.admin.schedules");

Route::apiResource("/customers", v1\CustomerController::class)->names("api.admin.customers");
Route::apiResource("/hospitals", v1\HospitalController::class)->names("api.admin.hospitals");
Route::apiResource("/phone-numbers", v1\PhoneNumberController::class)->names("api.admin.phone.numbers");
Route::apiResource("/available-departments", v1\AvailableDepartmentController::class)->names("api.admin.available.departments");
Route::apiResource("/specialities", v1\SpecialityController::class)->names("api.admin.specialities");
Route::apiResource("/addresses", v1\AddressController::class)->names("api.admin.addresses");
Route::apiResource('/cities', v1\CityController::class)->names('api.admin.cities');
