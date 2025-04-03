<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public welcome route (optional, keeping it for now)
Route::get('/welcome', function () {
    return Inertia::render('Welcome');
})->name('welcome');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); // Changed to root and uses controller

    // Project routes
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/calendar', [ProjectController::class, 'calendar'])->name('projects.calendar');

    // Resource routes
    Route::resource('resources', ResourceController::class);
    Route::put('/resources/{resource}/toggle-availability', [ResourceController::class, 'toggleAvailability'])->name('resources.toggle-availability');

    // Settings and Auth routes are included below
    require __DIR__.'/settings.php';
});

// Auth routes (typically outside the main auth group)
require __DIR__.'/auth.php';
