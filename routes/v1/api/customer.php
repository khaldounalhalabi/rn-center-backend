<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');

Route::get('/notifications', [v1\NotificationController::class, 'myNotifications'])->name('notifications.index');
Route::get('/notifications/{notificationId}/read', [v1\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/notifications/read-all', [v1\NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
Route::get('/notifications/unread-count', [v1\NotificationController::class, 'unreadCount'])->name('notifications.unread.count');

Route::post('/clinics/available-appointments-times', [v1\AvailableAppointmentTimeController::class, 'get'])->name('clinics.available.appointments.time');

Route::get('/appointments/{appointmentId}/cancel', [v1\AppointmentController::class, 'cancelAppointment'])->name('appointments.cancel');
Route::post('/appointments', [v1\AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/appointments', [v1\AppointmentController::class, 'getByCustomer'])->name('appointments.index');
Route::get('/appointments/{appointmentId}', [v1\AppointmentController::class, 'show'])->name('appointments.show');

Route::get('/prescriptions', [v1\PrescriptionController::class, 'index'])->name('prescriptions.index');
Route::get('/prescriptions/{prescriptionId}', [v1\PrescriptionController::class, 'show'])->name('prescriptions.show');

Route::get('/medical-records', [v1\MedicalRecordController::class, 'index'])->name('medical.records.index');
Route::get('/medical-records/{medicalRecordId}', [v1\MedicalRecordController::class, 'show'])->name('medical.records.show');

