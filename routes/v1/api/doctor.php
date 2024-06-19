<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;


//add-your-routes-here

Route::post('refresh', [v1\DoctorAuthController::class, 'refresh'])->middleware('auth:api')->name("refresh-token");
Route::post('logout', [v1\DoctorAuthController::class, 'logout'])->middleware('auth:api')->name("logout");
Route::post('update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::get('me', [v1\DoctorAuthController::class, 'userDetails'])->name('me');

Route::put('/clinic/update', [v1\ClinicController::class, 'updateDoctorClinic'])->name('clinic.update');
Route::get('/clinic', [v1\ClinicController::class, 'showDoctorClinic'])->name('clinic.show');

Route::post('/schedules', [v1\ScheduleController::class, 'storeUpdateSchedules'])->name('schedules.store');
Route::get('/schedules', [v1\ScheduleController::class, 'getCurrentClinicSchedules'])->name('schedules.show');

Route::get('clinic-holidays', [v1\ClinicHolidayController::class, 'getCurrentClinicHolidays'])->name('holidays');
Route::apiResource('clinic-holidays', v1\ClinicHolidayController::class)->except(['index'])->names('holidays');

Route::apiResource('services', v1\ServiceController::class)->names('services');

Route::apiResource('offers', v1\OfferController::class)->names('offers');


Route::get('customers', [v1\CustomerController::class, 'getDoctorCustomers'])->name('customers.index');
Route::get('customers/{customerId}', [v1\CustomerController::class, 'show'])->name('customers.show');
Route::post('customers', [v1\CustomerController::class, 'doctorAddCustomer'])->name('customers.store');
Route::put('customers/{customerId}', [v1\CustomerController::class, 'doctorUpdateCustomer'])->name('customers.update');
Route::delete('customers/{customerId}', [v1\CustomerController::class, 'doctorDeleteCustomer'])->name('customers.delete');

Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getCustomerPrescriptions'])->name('customer.prescriptions');
Route::apiResource('/prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::apiResource('medicines', v1\MedicineController::class)->names('medicines');
