<?php

use App\Http\Controllers\API\v1;
use App\Http\Controllers\API\v1\AdminAuthController;
use App\Http\Controllers\API\v1\CustomerAuthController;
use App\Http\Controllers\API\v1\DoctorAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')
    ->controller(CustomerAuthController::class)
    ->name('customer.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset.password.request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check.reset.password.code");
        Route::post('/reset-password', 'passwordReset')->name("password.reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
        Route::post('/register', 'register')->name("register");
    });

Route::prefix('/admin')
    ->controller(AdminAuthController::class)
    ->name('admin.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });


Route::prefix('doctor')
    ->controller(DoctorAuthController::class)
    ->name('doctor.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });

Route::prefix('secretary')
    ->controller(v1\SecretaryAuthController::class)
    ->name('doctor.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });

Route::get('/specialities/ranked', [v1\SpecialityController::class, 'getOrderedByClinicsCount'])->name('speciality.ranked');
Route::get('/specialities', [v1\SpecialityController::class, 'index'])->name('speciality.index');
Route::get('/service-categories', [v1\ServiceCategoryController::class, 'index'])->name('service.category.index');

Route::get('/clinics/{clinicId}', [v1\ClinicController::class, 'show'])->name('clinics.show');
Route::get('/clinics', [v1\ClinicController::class, 'index'])->name('clinics.index');
Route::get('/specialities/{specialityId}/clinics', [v1\ClinicController::class, 'getBySpeciality'])->name('specialities.clinics');

Route::get('/service-categories', [v1\ServiceCategoryController::class, 'index'])->name('service.categories.index');
Route::get('/service-categories/{serviceCategoryId}/services', [v1\ServiceController::class, 'getByCategory'])->name('service-categories.services');
