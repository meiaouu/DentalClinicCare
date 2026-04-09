<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Staff\AppointmentApprovalController;
use App\Http\Controllers\Staff\AppointmentController;
use App\Http\Controllers\Staff\AppointmentRequestController;
use App\Http\Controllers\Staff\ClinicScheduleController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
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
| Booking
|--------------------------------------------------------------------------
*/

Route::get('/book', [BookingController::class, 'entry'])->name('booking.entry');
Route::get('/book/guest', [BookingController::class, 'guestForm'])->name('booking.guest.form');

Route::middleware('auth')->group(function () {
    Route::get('/book/form', [BookingController::class, 'create'])->name('booking.create');
});

Route::post('/book/review', [BookingController::class, 'review'])->name('booking.review');
Route::post('/book/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/book/success/{requestCode}', [BookingController::class, 'success'])->name('booking.success');

Route::get('/booking/services/{service}/meta', [BookingController::class, 'serviceMeta'])->name('booking.service.meta');
Route::get('/booking/services/{service}/questions', [BookingController::class, 'serviceQuestions'])->name('booking.service.questions');
Route::get('/booking/available-dentists', [BookingController::class, 'availableDentists'])->name('booking.available.dentists');
Route::get('/booking/available-slots', [BookingController::class, 'availableSlots'])->name('booking.available.slots');

/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'internal.redirect'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

    Route::get('/appointment-requests', [AppointmentRequestController::class, 'index'])->name('staff.appointment-requests.index');
    Route::get('/appointment-requests/{appointmentRequest}', [AppointmentRequestController::class, 'show'])->name('staff.appointment-requests.show');
    Route::post('/appointment-requests/{appointmentRequest}/confirm', [AppointmentRequestController::class, 'confirm'])->name('staff.appointment-requests.confirm');
    Route::post('/appointment-requests/{appointmentRequest}/reject', [AppointmentRequestController::class, 'reject'])->name('staff.appointment-requests.reject');
    Route::post('/appointment-requests/{appointmentRequest}/reschedule', [AppointmentRequestController::class, 'reschedule'])->name('staff.appointment-requests.reschedule');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('staff.appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('staff.appointments.show');
    Route::post('/appointments/{appointment}/arrived', [AppointmentController::class, 'markArrived'])->name('staff.appointments.arrived');
    Route::post('/appointments/{appointment}/check-in', [AppointmentController::class, 'checkIn'])->name('staff.appointments.checkin');
    Route::post('/appointments/{appointment}/in-progress', [AppointmentController::class, 'markInProgress'])->name('staff.appointments.inprogress');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('staff.appointments.complete');
    Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('staff.appointments.noshow');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('staff.appointments.cancel');

    Route::get('/clinic-schedule', [ClinicScheduleController::class, 'index'])->name('staff.clinic-schedule.index');
    Route::post('/clinic-schedule/open-date', [ClinicScheduleController::class, 'openSpecificDate'])->name('staff.clinic-schedule.open-date');
    Route::post('/clinic-schedule/block', [ClinicScheduleController::class, 'blockDateOrTime'])->name('staff.clinic-schedule.block');

    Route::post('/appointments/approve', [AppointmentApprovalController::class, 'approve'])->name('staff.appointments.approve');
    Route::post('/appointments/reject', [AppointmentApprovalController::class, 'reject'])->name('staff.appointments.reject');
});

/*
|--------------------------------------------------------------------------
| Dentist
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Dentist\DashboardController as DentistDashboardController;
use App\Http\Controllers\Dentist\AvailabilityController as DentistAvailabilityController;

Route::middleware(['auth', 'role:dentist'])
    ->prefix('dentist')
    ->name('dentist.')
    ->group(function () {
        Route::get('/dashboard', [DentistDashboardController::class, 'index'])->name('dashboard');

        Route::get('/availability', [DentistAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [DentistAvailabilityController::class, 'storeOrUpdate'])->name('availability.store');

        Route::post('/unavailable-dates', [DentistAvailabilityController::class, 'storeUnavailableDate'])->name('unavailable-dates.store');
        Route::delete('/unavailable-dates/{unavailableDate}', [DentistAvailabilityController::class, 'destroyUnavailableDate'])->name('unavailable-dates.destroy');
    });

/*
|--------------------------------------------------------------------------
| Patient
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:patient'])->prefix('patient')->group(function () {
    Route::get('/dashboard', function () {
        return view('patient.dashboard');
    })->name('patient.dashboard');
});



Route::middleware('internal.redirect')->group(function () {

    Route::get('/', function () {
        $clinic = \App\Models\ClinicSetting::first();

        $services = \App\Models\Service::where('is_active', true)
            ->orderBy('service_name')
            ->get(['service_id', 'service_name', 'description']);

        return view('public.home', compact('clinic', 'services'));
    })->name('home');

    Route::get('/book', [BookingController::class, 'entry'])->name('booking.entry');
    Route::get('/book/guest', [BookingController::class, 'guestForm'])->name('booking.guest.form');

});






















Route::get('/force-logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
});















