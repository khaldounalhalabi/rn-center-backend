<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('refresh', [v1\CustomerAuthController::class, 'refresh'])->name('refresh.token');
Route::post('logout', [v1\CustomerAuthController::class, 'logout'])->name('logout');
Route::post('update-user-data', [v1\CustomerAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('me', [v1\CustomerAuthController::class, 'userDetails'])->name('me');
Route::post('/fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('/fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('/notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');
Route::get('/notifications/mark-all-as-read', [v1\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

Route::get('/appointments/today', [v1\AppointmentController::class, 'getCustomerTodayAppointments'])
    ->name('appointments.today');
Route::get('/appointments/{appointmentId}/cancel', [v1\AppointmentController::class, 'customerCancelAppointment'])->name('appointments.cancel');
Route::put('/appointments/{appointmentId}/change-date', [v1\AppointmentController::class, 'updateAppointmentDate'])
    ->name('appointments.change.date');
Route::apiResource('appointments', v1\AppointmentController::class)
    ->except(['destroy', 'update'])
    ->names('appointments');

Route::get('/hospitals', [v1\HospitalController::class, 'getByUserCity'])->name('hospitals');

Route::get('patient-profiles', [v1\PatientProfileController::class, 'getByCurrentCustomer'])->name('patient.profiles');
Route::get('patient-profiles/{patientProfileId}', [v1\PatientProfileController::class, 'show'])->name('patient.profiles.show');

Route::get('/clinics/{clinicId}/available-times', [v1\ClinicController::class, 'getClinicAvailableTimes'])->name('clinic.get.clinic.available.times');
