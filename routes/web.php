<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Routes temporaires
Route::get('/projects', function () {
    return inertia('Projects/Index');
})->name('projects.index');

Route::get('/tasks', function () {
    return inertia('Tasks/Index');
})->name('tasks.index');

Route::get('/calendar', function () {
    return inertia('Calendar');
})->name('calendar');