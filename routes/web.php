<?php

use App\Http\Controllers\QuestionController;
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
    Route::get('/list', [QuizController::class, 'fullList'])->name('quiz-list');
    Route::get('/my-list', [QuizController::class, 'myList'])->name('quiz-my-list');
    Route::get('/user/{userId}/list', [QuizController::class, 'userList'])->name('quiz-user-list');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/quiz')->group(function () {
        Route::get('/create', [QuizController::class, 'create'])->name('quiz-create');
        Route::post('/store', [QuizController::class, 'store'])->name('quiz-store');
        Route::get('/{quizId}/question/add', [QuestionController::class, 'create'])->name('quiz-question-add');
        Route::post('/{quizId}/question/store', [QuestionController::class, 'store'])->name('quiz-question-store');
    });
});
