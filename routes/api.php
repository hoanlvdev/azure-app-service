<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [App\Http\Controllers\API\Auth\AuthController::class, 'login']);

Route::middleware(['custom.sanctum.auth'])->group(function () {
    Route::get('/users', [App\Http\Controllers\API\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\API\UserController::class, 'create']);
    Route::post('/logout', [App\Http\Controllers\API\UserController::class, 'logout']);
    Route::get('/users-pagination', [App\Http\Controllers\API\UserController::class, 'getUser']);
});