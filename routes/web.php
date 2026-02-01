<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CapacityController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Models\Date;
use App\Models\Price;
use App\Models\TimeSlot;
use App\Services\TableAssignmentService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (TableAssignmentService $tableAssignmentService) {
    $dates = Date::orderBy('date_value')->get();
    $timeSlots = TimeSlot::all();
    $prices = Price::all();

    $slotAvailability = [];
    $soldOutDates = [];

    foreach ($dates as $date) {
        $allSlotsSoldOut = true;
        foreach ($timeSlots as $timeSlot) {
            $availableTables = $tableAssignmentService->getAvailableTables($date->id, $timeSlot->id);
            $availablePax = $availableTables->sum('capacity');
            $slotAvailability[$date->id][$timeSlot->id] = $availablePax;
            if ($availablePax > 0) {
                $allSlotsSoldOut = false;
            }
        }
        if ($allSlotsSoldOut) {
            $soldOutDates[] = $date->id;
        }
    }

    return view('welcome', compact('dates', 'timeSlots', 'prices', 'slotAvailability', 'soldOutDates'));
});

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');

Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/redirect', [PaymentController::class, 'redirect'])->name('payment.redirect');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/date/{date}/{timeSlot}', [AdminBookingController::class, 'byDate'])->name('bookings.by-date');
    Route::get('/bookings/availability', [AdminBookingController::class, 'availability'])->name('bookings.availability');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [AdminBookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{booking}/check-amendment-availability', [AdminBookingController::class, 'checkAmendmentAvailability'])->name('bookings.check-amendment-availability');
    Route::get('/capacity', [CapacityController::class, 'index'])->name('capacity.index');
    Route::get('/capacity/{date}/{timeSlot}/edit', [CapacityController::class, 'edit'])->name('capacity.edit');
    Route::put('/capacity/{date}/{timeSlot}', [CapacityController::class, 'update'])->name('capacity.update');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware('superadmin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/users', [SettingsController::class, 'store'])->name('settings.users.store');
    });
});
