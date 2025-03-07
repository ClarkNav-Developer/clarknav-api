<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocationSearchController;
use App\Http\Controllers\RouteUsageController;
use App\Http\Controllers\NavigationHistoryController;
use App\Http\Controllers\CustomRouteController;
use App\Http\Controllers\UserController;

// Public Routes

//Authentication
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');


Route::post('/feedback', [FeedbackController::class, 'store']);
Route::post('/location-searches', [LocationSearchController::class, 'store']);
Route::post('/route-usages', [RouteUsageController::class, 'store']);

// Authenticated User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/user-role', [UserController::class, 'getUserRole']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
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
Route::middleware(['auth:sanctum', 'isUser'])->group(function () {
    Route::get('/custom-routes', [CustomRouteController::class, 'index']);
    Route::post('/custom-routes', [CustomRouteController::class, 'store']);
    Route::get('/custom-routes/{id}', [CustomRouteController::class, 'show']);
    Route::put('/custom-routes/{id}', [CustomRouteController::class, 'update']);
    Route::delete('/custom-routes/{id}', [CustomRouteController::class, 'destroy']);

    Route::post('/navigation-histories', [NavigationHistoryController::class, 'store']);
    Route::get('/navigation-histories', [NavigationHistoryController::class, 'index']);
    Route::delete('/navigation-histories/{id}', [NavigationHistoryController::class, 'destroy']);
});