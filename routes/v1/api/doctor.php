<?php

use App\Http\Controllers\API\v1;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\ClinicHoliday;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Offer;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

//add-your-routes-here

Route::post('refresh', [v1\DoctorAuthController::class, 'refresh'])->middleware('auth:api')->name('refresh-token');
Route::post('logout', [v1\DoctorAuthController::class, 'logout'])->middleware('auth:api')->name('logout');
Route::post('update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::get('me', [v1\DoctorAuthController::class, 'userDetails'])->name('me');
Route::get('/available-times', [v1\ClinicController::class, 'getCurrentClinicAvailableTime'])->name('available-times');
Route::get('clinic-subscriptions', [v1\ClinicSubscriptionController::class, 'getCurrentClinicSubscriptions'])->name('clinic.subscriptions');

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

Route::get('clinic-holidays', [v1\ClinicHolidayController::class, 'getCurrentClinicHolidays'])->name('holidays');
Route::apiResource('clinic-holidays', v1\ClinicHolidayController::class)
    ->middleware([
        'staff_can:manage-holidays,' . ClinicHoliday::class,
    ])->except(['index'])->names('holidays');

Route::get('services', [v1\ServiceController::class, 'index'])->name('service.index');
Route::apiResource('services', v1\ServiceController::class)
    ->middleware([
        'staff_can:manage-services,' . Service::class,
    ])->except(['index'])->names('services');

Route::get('offers', [v1\OfferController::class, 'index'])->name('offer.index');
Route::apiResource('offers', v1\OfferController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-offers,' . Offer::class,
    ])->names('offers');

Route::get('customers', [v1\CustomerController::class, 'getDoctorCustomers'])->name('customers.index');
Route::get('customers/{customerId}', [v1\CustomerController::class, 'show'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.show');
Route::post('customers', [v1\CustomerController::class, 'doctorAddCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.store');
Route::put('customers/{customerId}', [v1\CustomerController::class, 'doctorUpdateCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.update');
Route::delete('customers/{customerId}', [v1\CustomerController::class, 'doctorDeleteCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.delete');

Route::get('/appointments/{appointmentId}/prescriptions', [v1\PrescriptionController::class, 'getAppointmentPrescriptions'])->name('appointments.prescriptions');
Route::delete('/prescriptions/medicine-data/{medicineDataId}', [v1\PrescriptionController::class, 'removeMedicine'])->name('prescription.medicine.remove');
Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getCustomerPrescriptions'])->name('customer.prescriptions');
Route::apiResource('/prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::get('medicines', [v1\MedicineController::class, 'index'])->name('medicines.index');
Route::apiResource('medicines', v1\MedicineController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-medicines,' . Medicine::class,
    ])->names('medicines');

Route::put('clinic-employees/{clinicEmployeeId}/update-permissions', [v1\ClinicEmployeeController::class, 'updateEmployeePermissions'])
    ->middleware([
        'staff_can:manage-employees,' . ClinicEmployee::class,
    ])
    ->name('clinic-employee.permissions-update');
Route::get('clinic-employees', [v1\ClinicEmployeeController::class, 'index'])
    ->name('clinic.employees.index');
Route::apiResource('/clinic-employees', v1\ClinicEmployeeController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-employees,' . ClinicEmployee::class,
    ])->names('clinic.employees');

Route::get('clinic-transactions/all', [v1\ClinicTransactionController::class, 'all'])->name('clinic.transaction.all');
Route::get('clinic-transactions/summary', [v1\ClinicTransactionController::class, 'summary'])->name('clinic.transactions.summary');
Route::get('clinic-transactions/export', [v1\ClinicTransactionController::class, 'export'])->name('clinic.transactions.export');
Route::apiResource('clinic-transactions', v1\ClinicTransactionController::class)->names('clinic.transactions');

Route::get('/appointments/all', [v1\AppointmentController::class, 'all'])->name('appointments.all');
Route::get('/appointments/export', [v1\AppointmentController::class, 'export'])->name('appointments.export');
Route::put('/appointments/{appointmentId}/update-date', [v1\AppointmentController::class, 'updateAppointmentDate'])->name('appointments.update.date');
Route::post('/appointments/{appointmentId}/toggle-status', [v1\AppointmentController::class, 'toggleAppointmentStatus'])->name('appointments.status.toggle');
Route::get('/appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('/appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');
Route::get('/customers/{customerId}/last-appointment', [v1\AppointmentController::class, 'getCustomerLastAppointment'])->name('customers.clinics.last-appointment');
Route::apiResource('/appointments', v1\AppointmentController::class)
    ->except(['destroy'])->names('appointments');

Route::get('/appointment-deductions/all', [v1\AppointmentController::class, 'all'])->name('appointment.deductions.all');
Route::get('appointment-deductions/summary', [v1\AppointmentDeductionController::class, 'clinicSummary'])->name('appointment.deduction.summary');
Route::get('appointment-deductions/export', [v1\AppointmentDeductionController::class, 'export'])
    ->name('appointment.deductions.export');
Route::get('appointment-deductions', [v1\AppointmentDeductionController::class, 'index'])
    ->name('appointment.deductions.index');
Route::get('appointment-deductions/{appointmentDeduction}', [v1\AppointmentDeductionController::class, 'show'])
    ->name('appointment.deductions.show');
