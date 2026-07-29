<?php

use App\Livewire\HealthWorker\Dashboard as HealthWorkerDashboard;
use App\Livewire\HealthWorker\RegisterPatient;
use App\Livewire\Midwife\Appointments;
use App\Livewire\Midwife\Dashboard as MidwifeDashboard;
use App\Livewire\Patient\Dashboard as PatientDashboard;
use App\Livewire\Patient\HealthInformation;
use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Patients\Record as PatientsRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
|
| Every page is a full-page Livewire component -- the route points straight
| at a class in app/Livewire. There are no controllers in this project.
|
| Login, logout, password reset, email verification and the two-factor
| challenge are NOT here: Laravel Fortify registers them. Adding a /login
| route to this file would silently override Fortify's and break 2FA.
|
| See DOCS/02-adding-a-page.md.
|
*/

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function (): void {

    // Single entry point after login. Sends each role to its own home page.
    Route::get('/dashboard', function () {
        return redirect()->route(Auth::user()->role->homeRoute());
    })->name('dashboard');

    /*
    | Midwife -- clinical records and appointments.
    */
    Route::middleware('role:midwife')
        ->prefix('midwife')
        ->name('midwife.')
        ->group(function (): void {
            Route::get('/dashboard', MidwifeDashboard::class)->name('dashboard');
            Route::get('/appointments', Appointments::class)->name('appointments');
        });

    /*
    | Shared staff pages -- both the midwife and the health worker need the
    | patient list. What each may actually change is decided by PatientPolicy
    | and ClinicalRecordPolicy, not by this route.
    */
    Route::middleware('role:midwife,health_worker')
        ->prefix('patients')
        ->name('patients.')
        ->group(function (): void {
            Route::get('/', PatientsIndex::class)->name('index');
            Route::get('/{patient}', PatientsRecord::class)->name('show');
        });

    /*
    | Health worker -- patient registration.
    */
    Route::middleware('role:health_worker')
        ->prefix('health-worker')
        ->name('health-worker.')
        ->group(function (): void {
            Route::get('/dashboard', HealthWorkerDashboard::class)->name('dashboard');
            Route::get('/register-patient', RegisterPatient::class)->name('register-patient');
        });

    /*
    | Patient portal -- read-only, as described in the study.
    */
    Route::middleware('role:patient')
        ->prefix('patient')
        ->name('patient.')
        ->group(function (): void {
            Route::get('/dashboard', PatientDashboard::class)->name('dashboard');
            Route::get('/my-health-information', HealthInformation::class)->name('my-health-information');
        });
});
