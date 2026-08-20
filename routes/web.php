<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Middleware\EnsureAuthenticated;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware([EnsureAuthenticated::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // JSON API Feed for FullCalendar & Resource Timeline
    Route::get('/api/events', [ActivityController::class, 'getEvents']);
    Route::get('/api/resources', [ActivityController::class, 'getResources']);
    Route::post('/api/check-conflicts', [ActivityController::class, 'checkConflicts']);

    // Activity CRUD APIs
    Route::post('/api/activities', [ActivityController::class, 'store']);
    Route::patch('/api/activities/{id}', [ActivityController::class, 'update']);
    Route::delete('/api/activities/{id}', [ActivityController::class, 'destroy']);
    
    // Notifications APIs
    Route::post('/api/notifications/{id}/read', [ActivityController::class, 'markNotificationRead']);
    Route::post('/api/notifications/{id}/delete', [ActivityController::class, 'deleteNotification']);
    
    // Notifications Page
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
});
