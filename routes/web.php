<?php

use App\Http\Controllers\Pms\BookingController;
use App\Http\Controllers\Pms\CalendarController;
use App\Http\Controllers\Pms\DashboardController;
use App\Http\Controllers\Pms\ReservationController;
use App\Http\Controllers\Pms\RoomInventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('booking', [BookingController::class, 'index'])->name('booking.index');
Route::get('booking/available', [BookingController::class, 'search'])->name('booking.available');
Route::get('booking/confirmation/{reservation:code}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('rooms', RoomInventoryController::class)->name('rooms.index');
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('calendar', CalendarController::class)->name('calendar.index');
});

require __DIR__.'/settings.php';
