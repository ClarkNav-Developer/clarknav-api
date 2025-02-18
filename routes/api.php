<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocationSearchController;
use App\Http\Controllers\RouteUsageController;
use App\Http\Controllers\NavigationHistoryController;
use App\Http\Controllers\CustomRouteController;
use App\Http\Controllers\UserController;

// Public Routes
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::post('/location-searches', [LocationSearchController::class, 'store']);
Route::post('/route-usages', [RouteUsageController::class, 'store']);

// Authenticated User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getAuthenticatedUser', [UserController::class, 'getAuthenticatedUser']);

    Route::post('/navigation-histories', [NavigationHistoryController::class, 'store']);
    Route::get('/navigation-histories', [NavigationHistoryController::class, 'index']);
    Route::delete('/navigation-histories/{id}', [NavigationHistoryController::class, 'destroy']);

    Route::get('/custom-routes', [CustomRouteController::class, 'index']);
    Route::post('/custom-routes', [CustomRouteController::class, 'store']);
    Route::get('/custom-routes/{id}', [CustomRouteController::class, 'show']);
    Route::put('/custom-routes/{id}', [CustomRouteController::class, 'update']);
    Route::delete('/custom-routes/{id}', [CustomRouteController::class, 'destroy']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
    Route::put('/feedback/{id}', [FeedbackController::class, 'update']);
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);

    Route::get('/location-searches', [LocationSearchController::class, 'index']);
    Route::get('/location-searches/{id}', [LocationSearchController::class, 'show']);
    Route::put('/location-searches/{id}', [LocationSearchController::class, 'update']);
    Route::delete('/location-searches/{id}', [LocationSearchController::class, 'destroy']);

    Route::get('/route-usages', [RouteUsageController::class, 'index']);
    Route::get('/route-usages/{id}', [RouteUsageController::class, 'show']);
    Route::put('/route-usages/{id}', [RouteUsageController::class, 'update']);
    Route::delete('/route-usages/{id}', [RouteUsageController::class, 'destroy']);
});

// User-Specific Routes
Route::middleware(['auth:sanctum', 'checkUser'])->group(function () {
    Route::get('/custom-routes', [CustomRouteController::class, 'index']);
    Route::post('/custom-routes', [CustomRouteController::class, 'store']);
    Route::get('/custom-routes/{id}', [CustomRouteController::class, 'show']);
    Route::put('/custom-routes/{id}', [CustomRouteController::class, 'update']);
    Route::delete('/custom-routes/{id}', [CustomRouteController::class, 'destroy']);
});