<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/quiz')->group(function () {
    Route::get('/list/{page?}', [QuizController::class, 'index']);
});

Route::middleware('auth')->group(function () {
    Route::prefix('/quiz')->group(function () {
        Route::get('/create', [QuizController::class, 'create']);
    });
});
