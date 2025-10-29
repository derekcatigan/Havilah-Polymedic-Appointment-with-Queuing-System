<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeDoctorController;
use App\Http\Controllers\ManageAccountController;
use App\Http\Controllers\ManageADSController;
use App\Http\Controllers\ManageAppointmentController;
use App\Http\Controllers\ManageDoctorController;
use App\Http\Controllers\ManagePatientController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffQueueController;
use App\Http\Controllers\WalkInController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/doctors/{specialty?}', [HomeDoctorController::class, 'index'])->name('home.doctor');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'authIndex'])->name('login');
    Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('auth.login');

    Route::get('/register', [AuthController::class, 'regisIndex'])->name('register');
    Route::post('/register/patient', [AuthController::class, 'regisPatient'])->name('auth.register');

    Route::get('/personnel/panel', [AuthController::class, 'personnelPanel'])->name('personnel.panel');
    Route::post('/personnel/auth', [AuthController::class, 'personnelAuth'])->name('personnel.auth');
});

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        // AdminDashboardController
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // ManageAccountController 
        Route::get('/admin/manage-account', [ManageAccountController::class, 'index'])->name('admin.manage.account');
        Route::get('/admin/create-account', [ManageAccountController::class, 'create'])->name('create.account');
        Route::post('/admin/account/store', [ManageAccountController::class, 'store'])->name('store.account');
        Route::get('/admin/account/edit/{id}', [ManageAccountController::class, 'edit'])->name('edit.account');
        Route::put('/admin/account/update/{id}', [ManageAccountController::class, 'update'])->name('update.account');
        Route::delete('/admin/account/delete/{id}', [ManageAccountController::class, 'destroy'])->name('delete.account');

        // ManageDoctorController
        Route::get('/admin/manage-doctor', [ManageDoctorController::class, 'index'])->name('admin.manage.doctor');
        Route::get('/admin/create-doctor', [ManageDoctorController::class, 'create'])->name('create.doctor');
        Route::post('/admin/doctor/store', [ManageDoctorController::class, 'store'])->name('store.doctor');
        Route::get('/admin/doctor/edit/{id}', [ManageDoctorController::class, 'edit'])->name('edit.doctor');
        Route::put('/admin/doctor/update/{id}', [ManageDoctorController::class, 'update'])->name('update.doctor');
        Route::delete('/admin/doctor/delete/{id}', [ManageDoctorController::class, 'destroy'])->name('delete.doctor');

        // ManageADSController
        Route::get('/admin/manage-ads', [ManageADSController::class, 'index'])->name('admin.manage.ads');
        Route::post('/admin/ads', [ManageADSController::class, 'store'])->name('admin.ads.store');
        Route::delete('/admin/ads/{ad}', [ManageADSController::class, 'destroy'])->name('admin.ads.destroy');

        // ManagePatientController
        Route::get('/admin/manage-patient', [ManagePatientController::class, 'index'])->name('admin.manage.patient');
    });

    // Doctor Routes
    Route::middleware('role:doctor')->group(function () {
        // DoctorDashboardController
        Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');

        // DoctorProfileController
        Route::get('/doctor/profile', [DoctorProfileController::class, 'index'])->name('doctor.profile');
        Route::put('/doctor/profile', [DoctorProfileController::class, 'update'])->name('doctor.profile.update');

        // DoctorAppointmentController
        Route::get('/doctor/my-appointments', [DoctorAppointmentController::class, 'index'])->name('doctor.appointment');
        Route::get('/doctor/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])
            ->name('doctor.appointments.show');
    });

    // Staff Routes
    Route::middleware('role:staff')->group(function () {
        // StaffDashboardController
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

        // ManageAppointmentController
        Route::get('/staff/manage-appointment', [ManageAppointmentController::class, 'index'])->name('manage.appointment');
        Route::get('/appointments/{appointment}', [ManageAppointmentController::class, 'show'])->name('staff.appointments.show');
        Route::post('/appointments/{appointment}/confirm', [ManageAppointmentController::class, 'confirm'])->name('staff.appointments.confirm');
        Route::post('/appointments/{appointment}/cancel', [ManageAppointmentController::class, 'cancel'])->name('staff.appointments.cancel');
        Route::post('/appointments/{appointment}/complete', [ManageAppointmentController::class, 'complete'])
            ->name('staff.appointments.complete');
        Route::delete('/staff/appointments/{appointment}', [ManageAppointmentController::class, 'destroy'])
            ->name('staff.appointments.destroy');


        Route::get('/walkin/create', [WalkInController::class, 'create'])->name('walkin.create');
        Route::post('/walkin', [WalkInController::class, 'store'])->name('walkin.store');

        Route::get('/staff/queue', [StaffQueueController::class, 'index'])->name('staff.queue.index');

        // Call Next
        // Route::post('/staff/queue/call-next', [StaffQueueController::class, 'callNext'])->name('staff.queue.callNext');

        // Individual Actions
        Route::post('/staff/queue/{queue}/call', [StaffQueueController::class, 'call'])->name('staff.queue.call');
        Route::post('/staff/queue/{queue}/progress', [StaffQueueController::class, 'progress'])->name('staff.queue.progress');
        Route::post('/staff/queue/{queue}/complete', [StaffQueueController::class, 'complete'])->name('staff.queue.complete');
        Route::post('/staff/queue/{queue}/skip', [StaffQueueController::class, 'skip'])->name('staff.queue.skip');
    });

    // Patient Routes
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/book/{id}', [HomeDoctorController::class, 'bookDoctor'])->name('book.doctor');


        Route::get('/my-appointments', [AppointmentController::class, 'myAppointments'])
            ->name('patient.appointments');
        Route::delete('/appointment/history/{id}', [AppointmentController::class, 'deleteHistory'])
            ->name('appointment.deleteHistory');
    });

    // AppointmentController Routes
    Route::post('/book/appointment/{doctorId}', [AppointmentController::class, 'book'])->name('book.appointment');
    Route::delete('/cancel/appointment/{id}', [AppointmentController::class, 'cancel'])
        ->name('cancel.appointment');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/personnel/logout', [AuthController::class, 'personnelLogout'])->name('personnel.logout');
});

Route::get('/force-logout', function (Request $request) {
    Auth::logout();

    $request->session()->regenerateToken();
    $request->session()->invalidate();
    Session::flush();

    return redirect()->route('login');
});
