<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
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
    return redirect('/quiz/list');
});

Auth::routes();

Route::prefix('/quiz')->group(function () {
    Route::get('/list', [QuizController::class, 'fullList'])->name('quiz-list');
    Route::get('/user/{user}/list', [QuizController::class, 'userList'])->name('quiz-user-list');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/quiz')->group(function () {
        Route::get('/create', [QuizController::class, 'create'])->name('quiz-create');
        Route::post('/store', [QuizController::class, 'store'])->name('quiz-store');
        Route::get('/{quiz}/question/add', [QuestionController::class, 'create'])->name('quiz-question-add');
        Route::post('/{quiz}/question/store', [QuestionController::class, 'store'])->name('quiz-question-store');

        Route::get('/my-list', [QuizController::class, 'myList'])->name('quiz-my-list');
    });
    Route::prefix('/test')->group(function () {
        Route::prefix('/quiz')->group(function () {
            Route::get('/{quiz}/question/{questionNumber}', [TestController::class, 'takeTestAttempt'])->name('take-test-attempt');
            Route::post('/{quiz}/save', [TestController::class, 'saveTestAttempt'])->name('save-attempt');
            Route::match(['GET', 'POST'],'/{quiz}/submit', [TestController::class, 'submitTest'])->name('submit-test');
        });
        Route::get('/{test}/result', [TestController::class, 'showTestResult'])->name('test-result');
    });
});
