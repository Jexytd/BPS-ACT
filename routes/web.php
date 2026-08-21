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

Route::get('/test-vercel', function () {
    return response()->json([
        'status' => 'ok',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'environment' => app()->environment(),
    ]);
});

// Protected Routes
Route::middleware([EnsureAuthenticated::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::get('/activities/list', [ActivityController::class, 'list'])->name('activities.list');
    Route::get('/calendar-test', [ActivityController::class, 'calendarTest'])->name('calendar.test');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // JSON API Feed for FullCalendar & Resource Timeline
    Route::get('/api/events', [ActivityController::class, 'getEvents']);
    Route::get('/api/resources', [ActivityController::class, 'getResources']);
    Route::post('/api/check-conflicts', [ActivityController::class, 'checkConflicts']);

    // Activity CRUD APIs
    Route::post('/api/activities', [ActivityController::class, 'store']);
    Route::match(['put', 'patch'], '/api/activities/{id}', [ActivityController::class, 'update']);
    Route::delete('/api/activities/{id}', [ActivityController::class, 'destroy']);
    
    // Notifications APIs
    Route::post('/api/notifications/{id}/read', [ActivityController::class, 'markNotificationRead']);
    Route::post('/api/notifications/{id}/delete', [ActivityController::class, 'deleteNotification']);
    
    // Notifications Page
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    
    // Borrowing System Web Pages
    Route::get('/assets', [\App\Http\Controllers\BorrowingController::class, 'indexAssets'])->name('assets.index');
    Route::get('/borrowings/create', [\App\Http\Controllers\BorrowingController::class, 'create'])->name('borrowings.create');
    Route::get('/borrowings', [\App\Http\Controllers\BorrowingController::class, 'indexMyRequests'])->name('borrowings.index');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Admin Only Routes
    Route::middleware('role:admin,lead')->group(function () {
        Route::get('/admin/borrowings', [\App\Http\Controllers\BorrowingController::class, 'indexAdminRequests'])->name('admin.borrowings');
        
        // User Management
        Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::patch('/admin/users/{id}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update_role');
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        // AI Assistant
        Route::get('/admin/ai-assistant', [\App\Http\Controllers\AiAgentController::class, 'index'])->name('ai.assistant');
        Route::post('/api/ai/analyze', [\App\Http\Controllers\AiAgentController::class, 'analyze']);

        // Admin Asset Management (CRUD)
        Route::post('/api/admin/assets', [\App\Http\Controllers\BorrowingController::class, 'storeAsset'])->name('admin.assets.store');
        Route::post('/api/admin/assets/{id}', [\App\Http\Controllers\BorrowingController::class, 'updateAsset'])->name('admin.assets.update');
        Route::delete('/api/admin/assets/{id}', [\App\Http\Controllers\BorrowingController::class, 'destroyAsset'])->name('admin.assets.destroy');
    });

    // Borrowing System APIs
    Route::get('/api/assets', [\App\Http\Controllers\BorrowingController::class, 'getAssets']);
    Route::get('/api/borrowings/my-requests', [\App\Http\Controllers\BorrowingController::class, 'getMyRequests']);
    Route::get('/api/borrowings/all', [\App\Http\Controllers\BorrowingController::class, 'getAllRequests']);
    Route::post('/api/borrowings', [\App\Http\Controllers\BorrowingController::class, 'storeRequest']);
    Route::patch('/api/borrowings/{id}/status', [\App\Http\Controllers\BorrowingController::class, 'updateStatus']);
    Route::delete('/api/borrowings/{id}', [\App\Http\Controllers\BorrowingController::class, 'destroy']);
});
