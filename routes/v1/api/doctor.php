<?php

use App\Http\Controllers\API\v1;
use App\Models\Clinic;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\DoctorAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\DoctorAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\DoctorAuthController::class, 'userDetails'])->name('user.details');
Route::post('fcm/store-token', [v1\DoctorAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [v1\DoctorAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('/contract/agree', [v1\ClinicController::class, 'agreeOnContract'])
    ->withoutMiddleware(['contract'])->name('contract.agree');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');

Route::get('available-times', [v1\ClinicController::class, 'getCurrentClinicAvailableTime'])->name('available-times');

Route::put('/clinic/update', [v1\ClinicController::class, 'updateDoctorClinic'])
    ->middleware([
        'staff_can:edit-clinic-profile,' . Clinic::class,
    ])->name('clinic.update');
Route::get('/clinic', [v1\ClinicController::class, 'showDoctorClinic'])
    ->name('clinic.show');

Route::post('/schedules', [v1\ScheduleController::class, 'storeUpdateSchedules'])->middleware([
    'staff_can:manage-schedules,' . Schedule::class,
])->name('schedules.store');
Route::get('/schedules', [v1\ScheduleController::class, 'getCurrentClinicSchedules'])
    ->middleware([
        'staff_can:manage-schedules,' . Schedule::class,
    ])->name('schedules.show');

Route::get('services/names', [v1\ServiceController::class, 'getClinicServicesNames'])->name('services.names');
Route::get('services', [v1\ServiceController::class, 'index'])->name('service.index');
Route::apiResource('services', v1\ServiceController::class)->except(['index'])->names('services');

Route::get('customers/{customerId}', [v1\CustomerController::class, 'show'])->name('customers.show');


Route::get('/appointments/{appointmentId}/prescriptions', [v1\PrescriptionController::class, 'getAppointmentPrescriptions'])->name('appointments.prescriptions');
Route::delete('/prescriptions/medicine-data/{medicineDataId}', [v1\PrescriptionController::class, 'removeMedicine'])->name('prescription.medicine.remove');
Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getCustomerPrescriptions'])->name('customer.prescriptions');
Route::apiResource('/prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::get('medicines', [v1\MedicineController::class, 'index'])->name('medicines.index');
Route::apiResource('medicines', v1\MedicineController::class)->except(['index'])->names('medicines');


Route::get('/appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('/appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');


Route::get('/statistics/index-page', [v1\StatisticsController::class, 'doctorIndexStatistics'])->name('doctor.index.statistics');
