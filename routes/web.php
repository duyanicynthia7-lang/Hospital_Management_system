<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
| NOTE: admin-only resource routes are registered BEFORE the shared ones so
| that URLs like /doctors/create are matched before /doctors/{doctor}.
*/
Route::middleware('auth')->group(function () {

    // ---------- Admin only ----------
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('wards', WardController::class);

        // Doctors: create / edit / delete are admin-only
        Route::resource('doctors', DoctorController::class)->except(['index', 'show']);

        // Patients: delete is admin-only
        Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    });

    // ---------- Doctor + Admin ----------
    Route::middleware('role:admin,doctor')->group(function () {
        Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
        Route::get('medical-records/{patient}', [MedicalRecordController::class, 'show'])->name('medical-records.show');

        Route::patch('admissions/{admission}/discharge', [AdmissionController::class, 'discharge'])->name('admissions.discharge');
        Route::resource('admissions', AdmissionController::class);
    });

    // ---------- All roles (admin, doctor, receptionist) ----------
    Route::middleware('role:admin,doctor,receptionist')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('patients', PatientController::class)->except(['destroy']);
        Route::resource('doctors', DoctorController::class)->only(['index', 'show']);
        Route::resource('appointments', AppointmentController::class);
    });
});
