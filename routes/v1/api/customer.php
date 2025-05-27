<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');

Route::get('/appointments/today', [v1\AppointmentController::class, 'getCustomerTodayAppointments'])
    ->name('appointments.today');
Route::get('/appointments/{appointmentId}/cancel', [v1\AppointmentController::class, 'customerCancelAppointment'])->name('appointments.cancel');
Route::put('/appointments/{appointmentId}/change-date', [v1\AppointmentController::class, 'updateAppointmentDate'])
    ->name('appointments.change.date');
Route::apiResource('appointments', v1\AppointmentController::class)
    ->except(['destroy', 'update'])
    ->names('appointments');

Route::get('/clinics/{clinicId}/available-times', [v1\ClinicController::class, 'getClinicAvailableTimes'])->name('clinic.get.clinic.available.times');
