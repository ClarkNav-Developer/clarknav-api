<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
Route::get('identity', [AuthController::class, 'getIdentity'])->middleware('auth:api');
Route::put('update-credentials/{id}', [AuthController::class, 'updateCredentials'])->middleware('auth:api');