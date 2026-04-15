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
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Staff\PatientController;
use App\Http\Controllers\Staff\NotificationController;
use App\Http\Controllers\Staff\MessageController;
use App\Http\Controllers\PublicMessageController;

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
Route::get('/booking/calendar-availability', [BookingController::class, 'calendarAvailability'])
    ->name('booking.calendar.availability');




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

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

        Route::get('/appointment-requests', [AppointmentRequestController::class, 'index'])->name('appointment-requests.index');
        Route::get('/appointment-requests/{appointmentRequest}', [AppointmentRequestController::class, 'show'])->name('appointment-requests.show');
        Route::post('/appointment-requests/{appointmentRequest}/confirm', [AppointmentRequestController::class, 'confirm'])->name('appointment-requests.confirm');
        Route::post('/appointment-requests/{appointmentRequest}/reject', [AppointmentRequestController::class, 'reject'])->name('appointment-requests.reject');
        Route::post('/appointment-requests/{appointmentRequest}/reschedule', [AppointmentRequestController::class, 'reschedule'])->name('appointment-requests.reschedule');

        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/arrived', [AppointmentController::class, 'markArrived'])->name('appointments.arrived');
        Route::post('/appointments/{appointment}/check-in', [AppointmentController::class, 'checkIn'])->name('appointments.checkin');
        Route::post('/appointments/{appointment}/in-progress', [AppointmentController::class, 'markInProgress'])->name('appointments.inprogress');
        Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.noshow');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

        Route::get('/clinic-schedule', [ClinicScheduleController::class, 'index'])->name('clinic-schedule.index');
        Route::post('/clinic-schedule/open-date', [ClinicScheduleController::class, 'openSpecificDate'])->name('clinic-schedule.open-date');
        Route::post('/clinic-schedule/block', [ClinicScheduleController::class, 'blockDateOrTime'])->name('clinic-schedule.block');

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/notifications', [NotificationController::class, 'index'])
    ->name('notifications.index');

Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
    ->name('notifications.read');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::get('/messages/{thread}', [MessageController::class, 'show'])->name('messages.show');
Route::post('/messages/{thread}/reply', [MessageController::class, 'reply'])->name('messages.reply');

Route::post('/patients/{patient}/messages', [MessageController::class, 'storePatientThread'])
    ->name('patients.messages.store');

Route::post('/appointment-requests/{appointmentRequest}/messages', [MessageController::class, 'storeGuestRequestThread'])
    ->name('appointment-requests.messages.store');

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

        Route::get('/schedule', function () {
            return view('dentist.schedule.index');
        })->name('schedule.index');

        Route::get('/patients/today', function () {
            return view('dentist.patients.today');
        })->name('patients.today');

        Route::get('/patients/charts', function () {
            return view('dentist.patients.charts');
        })->name('patients.charts');

        Route::get('/treatments', function () {
            return view('dentist.treatments.index');
        })->name('treatments.index');

        Route::get('/attachments', function () {
            return view('dentist.attachments.index');
        })->name('attachments.index');

        Route::get('/follow-ups', function () {
            return view('dentist.followups.index');
        })->name('followups.index');

        Route::get('/profile', function () {
            return view('dentist.profile.edit');
        })->name('profile.edit');

        Route::get('/availability', [DentistAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [DentistAvailabilityController::class, 'storeOrUpdate'])->name('availability.store');

        Route::post('/unavailable-dates', [DentistAvailabilityController::class, 'storeUnavailableDate'])->name('unavailable-dates.store');
        Route::delete('/unavailable-dates/{unavailableDate}', [DentistAvailabilityController::class, 'destroyUnavailableDate'])->name('unavailable-dates.destroy');

        Route::post('/availability/date-override', [DentistAvailabilityController::class, 'storeDateOverride'])
            ->name('availability.date-override.store');

        Route::delete('/availability/date-override/{dateOverride}', [DentistAvailabilityController::class, 'destroyDateOverride'])
            ->name('availability.date-override.destroy');
    });








/*
|--------------------------------------------------------------------------
| Patient
|--------------------------------------------------------------------------
*/



Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
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













/*
|--------------------------------------------------------------------------
| Messaging
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/messages/patient', [PublicMessageController::class, 'patientForm'])->name('messages.patient.form');
    Route::post('/messages/patient', [PublicMessageController::class, 'patientSend'])->name('messages.patient.send');
});

Route::get('/messages/guest/{requestCode}', [PublicMessageController::class, 'guestForm'])->name('messages.guest.form');
Route::post('/messages/guest/{requestCode}', [PublicMessageController::class, 'guestSend'])->name('messages.guest.send');




















Route::get('/force-logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
});















