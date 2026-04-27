<?php

use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\PlanningController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {

    Route::apiResource('goals', GoalController::class);
    Route::get('goals/{goal}/stats', [GoalController::class, 'stats']);

    Route::apiResource('tasks', TaskController::class);

    Route::apiResource('events', EventController::class);

    Route::get('planning/week', [PlanningController::class, 'weekView']);

    // AI routes get their own tighter rate limit
    Route::middleware('throttle:ai')->prefix('ai')->group(function () {
        Route::post('generate-planning',  [AIController::class, 'generatePlanning']);
        Route::post('suggest-time-slots', [AIController::class, 'suggestTimeSlots']);
        Route::post('analyze-optimize',   [AIController::class, 'analyzeAndOptimize']);
        Route::post('chat',               [AIController::class, 'chat']);
    });
});
