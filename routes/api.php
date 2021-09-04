<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('guest:sanctum')->group(function () {
    Route::post('/register', [AuthController::class , 'register']);
    Route::post('/login', [AuthController::class , 'authenticate']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout']);
    Route::match(['GET', 'POST'], '/auth', [AuthController::class , 'authenticatedUser']);
});
