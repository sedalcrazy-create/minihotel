<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Personnel
    Route::get('/personnel/template', [\App\Http\Controllers\PersonnelController::class, 'template'])->name('personnel.template');
    Route::get('/personnel/export', [\App\Http\Controllers\PersonnelController::class, 'export'])->name('personnel.export');
    Route::post('/personnel/import', [\App\Http\Controllers\PersonnelController::class, 'import'])->name('personnel.import');
    Route::resource('personnel', \App\Http\Controllers\PersonnelController::class);

    // Reservations
    Route::resource('reservations', ReservationController::class);
    Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
});
