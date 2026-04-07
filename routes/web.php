<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Staff\AppointmentApprovalController;
use App\Http\Controllers\Staff\ClinicScheduleController;
use App\Http\Controllers\Staff\AppointmentRequestReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\AppointmentRequestController;





Route::get('/available-slots', [BookingController::class, 'availableSlots'])->name('booking.slots');
/*
|--------------------------------------------------------------------------
| Public Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $clinic = \App\Models\ClinicSetting::first();

    $services = \App\Models\Service::where('is_active', true)
        ->orderBy('service_name')
        ->get(['service_id', 'service_name', 'description']);

    return view('public.home', compact('clinic', 'services'));
})->name('home');

/*
|--------------------------------------------------------------------------
| Booking Entry Flow
|--------------------------------------------------------------------------
| Guest users start at /book
| Logged-in users can go directly to the booking form
*/

Route::get('/book', [BookingController::class, 'entry'])->name('booking.entry');

Route::get('/book/guest', [BookingController::class, 'guestForm'])
    ->name('booking.guest.form');

Route::middleware('auth')->group(function () {
    Route::get('/book/form', [BookingController::class, 'create'])->name('booking.create');
});

/*
|--------------------------------------------------------------------------
| Booking Review / Submit
|--------------------------------------------------------------------------
*/

Route::post('/book/review', [BookingController::class, 'review'])->name('booking.review');
Route::post('/book/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/book/success/{requestCode}', [BookingController::class, 'success'])->name('booking.success');

/*
|--------------------------------------------------------------------------
| Booking AJAX / Dynamic Data
|--------------------------------------------------------------------------
| Used by booking form for service metadata, questions,
| available dentists, and available slots
*/

Route::get('/booking/services/{service}/meta', [BookingController::class, 'serviceMeta'])
    ->name('booking.service.meta');

Route::get('/booking/services/{service}/questions', [BookingController::class, 'serviceQuestions'])
    ->name('booking.service.questions');

Route::get('/booking/available-dentists', [BookingController::class, 'availableDentists'])
    ->name('booking.available.dentists');

Route::get('/booking/available-slots', [BookingController::class, 'availableSlots'])
    ->name('booking.available.slots');

/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Staff - Clinic Schedule Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/clinic-schedule', [ClinicScheduleController::class, 'index'])
        ->name('staff.clinic-schedule.index');

    Route::post('/clinic-schedule/open-date', [ClinicScheduleController::class, 'openSpecificDate'])
        ->name('staff.clinic-schedule.open-date');

    Route::post('/clinic-schedule/block', [ClinicScheduleController::class, 'blockDateOrTime'])
        ->name('staff.clinic-schedule.block');
});

/*
|--------------------------------------------------------------------------
| Staff - Appointment Approval Flow
|--------------------------------------------------------------------------
| Staff reviews pending appointment requests and can approve or reject them
*/

Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::post('/appointments/approve', [AppointmentApprovalController::class, 'approve'])
        ->name('staff.appointments.approve');

    Route::post('/appointments/reject', [AppointmentApprovalController::class, 'reject'])
        ->name('staff.appointments.reject');
});




/*
|--------------------------------------------------------------------------
| Staff - Appointment Request Controllers
|--------------------------------------------------------------------------
|
*/

Route::prefix('staff')->middleware('role:staff')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');

    Route::get('/appointment-requests', [AppointmentRequestController::class, 'index'])->name('staff.appointment-requests.index');
    Route::get('/appointment-requests/{appointmentRequest}', [AppointmentRequestController::class, 'show'])->name('staff.appointment-requests.show');
    Route::post('/appointment-requests/{appointmentRequest}/confirm', [AppointmentRequestController::class, 'confirm'])->name('staff.appointment-requests.confirm');
    Route::post('/appointment-requests/{appointmentRequest}/reject', [AppointmentRequestController::class, 'reject'])->name('staff.appointment-requests.reject');
    Route::post('/appointment-requests/{appointmentRequest}/reschedule', [AppointmentRequestController::class, 'reschedule'])->name('staff.appointment-requests.reschedule');
});











/*
|--------------------------------------------------------------------------
| Authenticated Dashboards
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    });

    Route::prefix('staff')->middleware('role:staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');

    Route::get('/appointment-requests', [AppointmentRequestReviewController::class, 'index'])
        ->name('staff.appointment-requests.index');

    Route::get('/appointment-requests/{requestId}', [AppointmentRequestReviewController::class, 'show'])
        ->name('staff.appointment-requests.show');

    Route::post('/appointment-requests/{requestId}/confirm', [AppointmentRequestReviewController::class, 'confirm'])
        ->name('staff.appointment-requests.confirm');

    Route::post('/appointment-requests/{requestId}/reject', [AppointmentRequestReviewController::class, 'reject'])
        ->name('staff.appointment-requests.reject');

    Route::post('/appointment-requests/{requestId}/reschedule', [AppointmentRequestReviewController::class, 'reschedule'])
        ->name('staff.appointment-requests.reschedule');



    });

    Route::prefix('dentist')->middleware('role:dentist')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dentist'])->name('dentist.dashboard');
    });

    Route::prefix('patient')->middleware('role:patient')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'patient'])->name('patient.dashboard');
    });
});
