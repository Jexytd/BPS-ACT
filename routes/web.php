<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Middleware\EnsureAuthenticated;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware([EnsureAuthenticated::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // JSON API Feed for FullCalendar & Resource Timeline
    Route::get('/api/events', [ActivityController::class, 'getEvents']);
    Route::get('/api/resources', [ActivityController::class, 'getResources']);
    Route::post('/api/check-conflicts', [ActivityController::class, 'checkConflicts']);

    // Activity CRUD APIs
    Route::post('/api/activities', [ActivityController::class, 'store']);
    Route::patch('/api/activities/{id}', [ActivityController::class, 'update']);
    Route::delete('/api/activities/{id}', [ActivityController::class, 'destroy']);
});
