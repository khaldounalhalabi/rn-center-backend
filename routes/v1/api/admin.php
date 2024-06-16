<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

//add-your-routes-here

Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name('refresh-token');
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');
Route::get('/me', [v1\AdminAuthController::class, 'userDetails'])->name('me');
Route::post('/fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('/fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::delete('/users/{userId}/toggle-archive', [v1\UserController::class, 'toggleArchive'])->name('users.toggle.archive');
Route::get('/users/{userId}/toggle-block', [v1\UserController::class, 'toggleBlock'])->name('user.block.toggle');
Route::apiResource('/users', v1\UserController::class)->names('users');

Route::get('/clinics/{clinicId}/toggle-status', [v1\ClinicController::class, 'toggleClinicStatus'])->name('clinic.status.toggle');
Route::get('/subscriptions/{subscriptionId}/clinics', [v1\ClinicController::class, 'getBySubscription'])->name('subscription.clinics');
Route::apiResource('/clinics', v1\ClinicController::class)->names('clinics');

Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::post('schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
        Route::get('/clinics/{clinicId}/appointments', [v1\AppointmentController::class, 'getClinicAppointments'])->name('clinics.appointments');
        Route::get('/clinics/{clinicId}/available-times', [v1\ClinicController::class, 'getClinicAvailableTimes'])->name('clinic.get.clinic.available.times');
    });

Route::get('customers/{customerId}/patient-profiles', [v1\PatientProfileController::class, 'getCustomerPatientProfiles']);
Route::apiResource('/customers', v1\CustomerController::class)->names('customers');

Route::get('/hospitals/{hospitalId}/toggle-status', [v1\HospitalController::class, 'toggleHospitalStatus'])->name('hospitals.toggle.status');
Route::apiResource('/hospitals', v1\HospitalController::class)->names('hospitals');

Route::apiResource('/phone-numbers', v1\PhoneNumberController::class)->names('phone.numbers');

Route::apiResource('/available-departments', v1\AvailableDepartmentController::class)->names('available.departments');

Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');

Route::apiResource('/addresses', v1\AddressController::class)->names('addresses');

Route::apiResource('/cities', v1\CityController::class)->names('cities');

Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)->names('clinic.holidays');

Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::apiResource('/services', v1\ServiceController::class)->names('services');

Route::post('appointments/{appointmentId}/toggle-status', [v1\AppointmentController::class, 'toggleAppointmentStatus'])->name('appointments.status.toggle');
Route::get('appointments/{appointmentId}/prescriptions/', [v1\PrescriptionController::class, 'getAppointmentPrescriptions'])->name('appointments.prescriptions');
Route::get('appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');
Route::apiResource('/appointments', v1\AppointmentController::class)->names('appointments');

Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');

Route::delete('/prescriptions/medicine-data/{medicineDataId}', [v1\PrescriptionController::class, 'removeMedicine'])->name('prescription.medicine.remove');
Route::apiResource('prescriptions', v1\PrescriptionController::class)->names('prescriptions');

Route::apiResource('/blocked-items', v1\BlockedItemController::class)->names('blocked.items');

Route::apiResource('/subscriptions', v1\SubscriptionController::class)->names('subscriptions');

Route::post('/enquiries/{enquiryId}/reply', [v1\EnquiryController::class, 'reply'])->name('enquiries.reply');
Route::apiResource('/enquiries', v1\EnquiryController::class)
    ->except(['update', 'store', 'destroy'])
    ->names('enquiries');

Route::get('clinics/{clinicId}/subscriptions', [v1\ClinicSubscriptionController::class, 'getByClinic'])->name('clinics.subscriptions');
Route::apiResource('/clinic-subscriptions', v1\ClinicSubscriptionController::class)
    ->except(['index'])
    ->names('clinic.subscriptions');

Route::apiResource('offers', v1\OfferController::class)->names('offers');

Route::apiResource('/patient-profiles', v1\PatientProfileController::class)->names('patient.profiles');

Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');

Route::apiResource('/blood-donation-requests', v1\BloodDonationRequestController::class)->names('blood.donation.requests');
