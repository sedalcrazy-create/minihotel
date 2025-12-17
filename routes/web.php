<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CleaningController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;

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

    // Guests
    Route::resource('guests', GuestController::class);

    // Meals
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
    Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
    Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

    // Cleaning
    Route::get('/cleaning', [CleaningController::class, 'index'])->name('cleaning.index');
    Route::get('/cleaning/pending', [CleaningController::class, 'pending'])->name('cleaning.pending');
    Route::post('/cleaning', [CleaningController::class, 'store'])->name('cleaning.store');

    // Maintenance
    Route::resource('maintenance', MaintenanceController::class);
    Route::put('/maintenance/{maintenance}/assign', [MaintenanceController::class, 'assign'])->name('maintenance.assign');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/reservations', [ReportController::class, 'reservations'])->name('reports.reservations');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/meals', [ReportController::class, 'meals'])->name('reports.meals');
});
