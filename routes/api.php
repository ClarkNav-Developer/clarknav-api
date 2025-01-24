<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\FeedbackController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('get-identity', [AuthController::class, 'getIdentity'])->middleware('auth:api');
Route::put('update-credentials/{id}', [AuthController::class, 'updateCredentials'])->middleware('auth:api');

Route::post('bug-reports', [BugReportController::class, 'store'])->middleware('auth:api');
Route::post('feedback', [FeedbackController::class, 'store'])->middleware('auth:api');