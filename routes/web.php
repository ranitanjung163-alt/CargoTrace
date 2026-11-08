<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create')->middleware('role:admin,operator');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store')->middleware('role:admin,operator');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit')->middleware('role:admin,operator');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update')->middleware('role:admin,operator');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy')->middleware('role:admin');
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'transitionStatus'])->name('shipments.status')->middleware('role:admin,operator');

    // Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('tracking.store')->middleware('role:admin,operator');

    // Exceptions
    Route::get('/exceptions', [ExceptionController::class, 'index'])->name('exceptions.index');
    Route::post('/shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('exceptions.store')->middleware('role:admin,operator');
    Route::patch('/exceptions/{exception}/status', [ExceptionController::class, 'updateStatus'])->name('exceptions.status')->middleware('role:admin,operator');

    // Carriers
    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index')->middleware('role:admin,operator');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store')->middleware('role:admin');
    Route::put('/carriers/{carrier}', [CarrierController::class, 'update'])->name('carriers.update')->middleware('role:admin');

    // Locations
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index')->middleware('role:admin,operator');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store')->middleware('role:admin');
    Route::get('/locations/geocode', [LocationController::class, 'geocode'])->name('locations.geocode')->middleware('role:admin,operator');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    // Admin Only: Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index')->middleware('role:admin');

    // Admin Only: Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role:admin');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('role:admin');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('role:admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('role:admin');
});
