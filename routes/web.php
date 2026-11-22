<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingEventController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Halaman utama.
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Penggantian bahasa untuk pengunjung dan pengguna login.
Route::post('/language', [LanguageController::class, 'update'])
    ->name('language.update');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login.
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    // Registration.
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    // Main Dashboard.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Notification Center.
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');

    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    // Shipment Management.

    // Menampilkan daftar shipment.
    Route::get('/shipments', [ShipmentController::class, 'index'])
        ->middleware('role:admin,operator,customer')
        ->name('shipments.index');

    // Menampilkan formulir tambah shipment.
    Route::get('/shipments/create', [ShipmentController::class, 'create'])
        ->middleware('role:admin,operator')
        ->name('shipments.create');

    // Menyimpan shipment baru.
    Route::post('/shipments', [ShipmentController::class, 'store'])
        ->middleware('role:admin,operator')
        ->name('shipments.store');

    // Menyimpan pembaruan tracking (khusus admin dan operator).
    Route::post('/shipments/{shipment}/tracking', [TrackingEventController::class, 'store'])
        ->middleware('role:admin,operator')
        ->name('shipments.tracking.store');

    // Menampilkan detail shipment.
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])
        ->middleware('role:admin,operator,customer')
        ->name('shipments.show');

    // Exception Management.

    // Daftar exception sesuai hak akses pengguna.
    Route::get('/exceptions', [ShipmentExceptionController::class, 'index'])
        ->middleware('role:admin,operator,customer')
        ->name('exceptions.index');

    // Penugasan exception hanya untuk admin.
    Route::patch('/exceptions/{exception}/assign', [ShipmentExceptionController::class, 'assign'])
        ->middleware('role:admin')
        ->name('exceptions.assign');

    // Pembaruan status exception untuk admin dan operator.
    Route::patch('/exceptions/{exception}', [ShipmentExceptionController::class, 'update'])
        ->middleware('role:admin,operator')
        ->name('exceptions.update');

    // Logout.
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
