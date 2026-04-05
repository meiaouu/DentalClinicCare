<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::middleware('guest')->group(function () {
    Route::get('/book/guest', [BookingController::class, 'guestForm'])->name('booking.guest.form');
});

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







Route::middleware('guest')->group(function () {
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
    });

    Route::prefix('dentist')->middleware('role:dentist')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dentist'])->name('dentist.dashboard');
    });

    Route::prefix('patient')->middleware('role:patient')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'patient'])->name('patient.dashboard');
    });
});

