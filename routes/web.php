<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::redirect('/', '/login');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');

    // Activity Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('logs/export', [LogController::class, 'export'])->name('logs.export');
    Route::delete('logs/{log}', [LogController::class, 'destroy'])->name('logs.destroy');
    Route::post('logs/cleanup', [LogController::class, 'cleanup'])->name('logs.cleanup');
    Route::post('logs/clear', [LogController::class, 'clear'])->name('logs.clear');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
