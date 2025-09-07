<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard widget management
    Route::post('/dashboard/widgets', [DashboardController::class, 'createWidget'])->name('dashboard.widgets.create');
    Route::patch('/dashboard/widgets/{widget}', [DashboardController::class, 'updateWidget'])->name('dashboard.widgets.update');
    Route::delete('/dashboard/widgets/{widget}', [DashboardController::class, 'deleteWidget'])->name('dashboard.widgets.delete');
    
    // Group management
    Route::resource('groups', GroupController::class);
    Route::post('/groups/{group}/move', [GroupController::class, 'move'])->name('groups.move');
    Route::get('/groups/{group}/export', [GroupController::class, 'export'])->name('groups.export');
    
    // Credential management
    Route::resource('credentials', CredentialController::class);
    Route::post('/credentials/{credential}/favorite', [CredentialController::class, 'toggleFavorite'])->name('credentials.favorite');
    Route::post('/credentials/generate-password', [CredentialController::class, 'generatePassword'])->name('credentials.generate-password');
    Route::get('/credentials/{credential}/password', [CredentialController::class, 'getPassword'])->name('credentials.password');
    
    // Search functionality
    Route::get('/search', [CredentialController::class, 'search'])->name('search');
    
    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
