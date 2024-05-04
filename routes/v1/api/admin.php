<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

//add-your-routes-here

Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name("refresh-token");
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update-user-data');

Route::delete('/users/{userId}/toggle-archive', [v1\UserController::class, 'toggleArchive'])->name('users.toggle.archive');

Route::get('/clinics/{clinicId}/toggle-status', [v1\ClinicController::class, 'toggleClinicStatus'])->name('clinic.status.toggle');
Route::apiResource("/clinics", v1\ClinicController::class)->names("clinics");

Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::post('schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
        Route::get('/clinics/{clinicId}/appointments', [v1\AppointmentController::class, 'getClinicAppointments'])->name('clinics.appointments');
        Route::get('/clinics/{clinicId}/available-times', [v1\ClinicController::class, 'getClinicAvailableTimes'])->name('clinic.get.clinic.available.times');
    });

Route::apiResource("/customers", v1\CustomerController::class)->names("customers");
Route::get('/hospitals/all', [v1\HospitalController::class, 'getAll'])->name('hospital.all');
Route::apiResource("/hospitals", v1\HospitalController::class)->names("hospitals");
Route::apiResource("/phone-numbers", v1\PhoneNumberController::class)->names("phone.numbers");
Route::apiResource("/available-departments", v1\AvailableDepartmentController::class)->names("available.departments");
Route::apiResource("/specialities", v1\SpecialityController::class)->names("specialities");
Route::apiResource("/addresses", v1\AddressController::class)->names("addresses");
Route::apiResource('/cities', v1\CityController::class)->names('cities');
Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)->names('clinic.holidays');

Route::post('/service-categories/export', [v1\ServiceCategoryController::class, 'export'])->name('service.categories.export');
Route::post('/service-categories/import', [v1\ServiceCategoryController::class, 'import'])->name('service.categories.import');
Route::get('/service-categories/get-import-example', [v1\ServiceCategoryController::class, 'getImportExample'])->name('service.categories.get.example');
Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::post('/services/export', [v1\ServiceController::class, 'export'])->name('services.export');
Route::post('/services/import', [v1\ServiceController::class, 'import'])->name('services.import');
Route::get('/services/get-import-example', [v1\ServiceController::class, 'getImportExample'])->name('services.get.example');
Route::apiResource('/services', v1\ServiceController::class)->names('services');

Route::get('appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');
Route::apiResource('/appointments', v1\AppointmentController::class)->names('appointments');
Route::prefix('appointments')
    ->name('appointments.')
    ->controller(v1\AppointmentController::class)
    ->group(function () {
        Route::post('/export', 'export')->name('export');
        Route::post('/import', 'import')->name('import');
        Route::get('/get-import-example', 'getImportExample')->name('get.example');
    });


Route::post('/medicines/export', [v1\MedicineController::class, 'export'])->name('medicines.export');
Route::post('/medicines/import', [v1\MedicineController::class, 'import'])->name('medicines.import');
Route::get('/medicines/get-import-example', [v1\MedicineController::class, 'getImportExample'])->name('medicines.get.example');
Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');
