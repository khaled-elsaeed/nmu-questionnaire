<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\ActionLogController;
use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuestionModules\QuestionModulesController;
use App\Http\Controllers\QuestionModules\QuestionsController;
use App\Http\Controllers\Questionnaires\QuestionnairesController;



Route::get('/', [LoginController::class, 'showLoginPage'])->name('home'); 
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login'); 
Route::post('/login', [LoginController::class, 'login'])->name('login.post'); 


Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/home', [AdminHomeController::class, 'showHomePage'])->name('home');

        Route::prefix('question-modules')->name('question-modules.')->group(function () {
            Route::get('/', [QuestionModulesController::class, 'index'])->name('index');
            Route::get('/create', [QuestionModulesController::class, 'create'])->name('create');
            Route::post('/store', [QuestionModulesController::class, 'store'])->name('store');
            
            Route::delete('/{id}', [QuestionModulesController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [QuestionModulesController::class, 'getModuleDetailsWithQuestions'])->name('module');

            // New route for fetching questions by module ID
            Route::get('/{module}/questions', [QuestionModulesController::class, 'getQuestions'])->name('questions');
        });

        Route::prefix('questions')->name('questions.')->group(function () {
            Route::delete('/{id}', [QuestionsController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/options', [QuestionModulesController::class, 'getOptions'])->name('options');

        });

        Route::prefix('questionnaires')->name('questionnaires.')->group(function () {
            Route::get('/', [QuestionnairesController::class, 'index'])->name('index');

            Route::get('/create', [QuestionnairesController::class, 'create'])->name('create');
            Route::post('/store', [QuestionnairesController::class, 'store'])->name('store');

        });


    });

    Route::get('/student/home', [StudentHomeController::class, 'showHomePage'])->name('student.home');
});




Route::post('/action-logs', [ActionLogController::class, 'store']);
Route::get('/action-logs', [ActionLogController::class, 'index']);



Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

use App\Http\Controllers\Admin\UserManagement\FacultiesController;
use App\Http\Controllers\Admin\UserManagement\DepartmentsController;

Route::get('/faculties/{id}/departments', [FacultiesController::class, 'getDepartments'])->name('faculties.departments');
Route::get('/departments/{id}/programs', [DepartmentsController::class, 'getPrograms'])->name('departments.programs');