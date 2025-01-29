<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\FeedbackController;

// Auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('get-identity', [AuthController::class, 'getIdentity'])->middleware('auth:api');
    Route::put('update-credentials/{id}', [AuthController::class, 'updateCredentials'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Public routes
Route::post('bug-reports', [BugReportController::class, 'store']);
Route::post('feedback', [FeedbackController::class, 'store']);

// Admin routes
Route::middleware(['auth:api', 'checkAdmin'])->group(function () {
    Route::get('bug-reports', [BugReportController::class, 'index']);
    Route::get('bug-reports/{id}', [BugReportController::class, 'show']);
    Route::put('bug-reports/{id}', [BugReportController::class, 'update']);
    Route::delete('bug-reports/{id}', [BugReportController::class, 'destroy']);

    Route::get('feedback', [FeedbackController::class, 'index']);
    Route::get('feedback/{id}', [FeedbackController::class, 'show']);
    Route::put('feedback/{id}', [FeedbackController::class, 'update']);
    Route::delete('feedback/{id}', [FeedbackController::class, 'destroy']);
});