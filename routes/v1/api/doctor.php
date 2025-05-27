<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\DoctorAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\DoctorAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\DoctorAuthController::class, 'userDetails'])->name('user.details');
Route::post('fcm/store-token', [v1\DoctorAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [v1\DoctorAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::prefix('appointments')
    ->name('appointments.')
    ->group(function () {
        Route::get('/', [v1\AppointmentController::class, 'index'])->name('index');
        Route::get('/{appointmentId}', [v1\AppointmentController::class, 'show'])->name('show');
    });

Route::resource('prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::post('available-appointments-times', [v1\AvailableAppointmentTimeController::class, 'get'])->name('available.appointments.time');

Route::get('/medicines', [v1\MedicineController::class, 'index'])->name('medicine.index');
Route::get('/medicines/{medicineId}', [v1\MedicineController::class, 'show'])->name('medicine.show');
Route::post('/medicines', [v1\MedicineController::class, 'store'])->name('medicine.store');

Route::get('/customers', [v1\CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customerId}', [v1\CustomerController::class, 'show'])->name('customers.show');
Route::put('/customers/{customerId}', [v1\CustomerController::class, 'update'])->name('customers.update');
Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getByCustomer'])->name('customers.prescriptions');


