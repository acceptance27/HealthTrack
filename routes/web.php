<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\MedicalHistoryController as PatientMedicalHistoryController;
use App\Http\Controllers\Midwife\DashboardController as MidwifeDashboardController;
use App\Http\Controllers\Midwife\PatientController as MidwifePatientController;
use App\Http\Controllers\Midwife\AppointmentController as MidwifeAppointmentController;
use App\Http\Controllers\Midwife\MedicalRecordController;
use App\Http\Controllers\Midwife\InventoryController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return redirect()->route(match (Auth::user()->role->value) {
            'midwife' => 'midwife.dashboard',
            'patient' => 'patient.dashboard',
            default => 'admin.dashboard',
        });
    })->name('dashboard');

    // Patient routes
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function (): void {
        Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');
        Route::get('/my-health-information', [PatientMedicalHistoryController::class, 'index'])->name('my-health-information');
    });

    // Midwife routes
    Route::middleware('role:midwife')->prefix('midwife')->name('midwife.')->group(function (): void {
        Route::get('/dashboard', MidwifeDashboardController::class)->name('dashboard');
        Route::get('/patients', [MidwifePatientController::class, 'index'])->name('patients');
        Route::get('/patients/{id}', [MidwifePatientController::class, 'show'])->name('patients.show');
        Route::post('/patients/{id}/appointments', [MidwifePatientController::class, 'storeAppointment'])->name('patients.appointments.store');
        Route::delete('/patients/{id}/appointments/{appointment}', [MidwifePatientController::class, 'destroyAppointment'])->name('patients.appointments.destroy');
        Route::get('/appointments', [MidwifeAppointmentController::class, 'index'])->name('appointments');
        Route::get('/appointments/{id}', [MidwifeAppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical-records');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/inventory/vaccines', [InventoryController::class, 'vaccines'])->name('inventory.vaccines');
        Route::get('/inventory/medicines', [InventoryController::class, 'medicines'])->name('inventory.medicines');
    });

    // Admin routes
    Route::middleware('role:admin')->group(function (): void {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    });
});
