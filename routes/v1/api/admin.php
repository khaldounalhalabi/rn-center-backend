<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

//add-your-routes-here
Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name("refresh-token");
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');

Route::apiResource("/clinics", v1\ClinicController::class)->names("clinics");

Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules' , 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::post('schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
    });


Route::apiResource("/customers", v1\CustomerController::class)->names("customers");
Route::get('/hospitals/all', [v1\HospitalController::class, 'getAll'])->name('hospital.all');
Route::apiResource("/hospitals", v1\HospitalController::class)->names("hospitals");
Route::apiResource("/phone-numbers", v1\PhoneNumberController::class)->names("phone.numbers");
Route::apiResource("/available-departments", v1\AvailableDepartmentController::class)->names("available.departments");
Route::apiResource("/specialities", v1\SpecialityController::class)->names("specialities");
Route::apiResource("/addresses", v1\AddressController::class)->names("addresses");
Route::apiResource('/cities', v1\CityController::class)->names('cities');
Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)
    ->except('update')
    ->names('clinic.holidays');
