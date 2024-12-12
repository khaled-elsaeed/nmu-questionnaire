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
use App\Http\Controllers\Responder\ResponderHomeController;
use App\Http\Controllers\Responder\ResponderQuestionnaireController;
use App\Http\Controllers\Admin\UserManagement\FacultiesController;
use App\Http\Controllers\Admin\UserManagement\DepartmentsController;

/**
 * Public Routes
 */
Route::get('/', [LoginController::class, 'showLoginPage'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

/**
 * Action Logs Routes
 */
Route::post('/action-logs', [ActionLogController::class, 'store']);
Route::get('/action-logs', [ActionLogController::class, 'index']);

/**
 * Faculty and Department Routes
 */
Route::get('/faculties/{id}/departments', [FacultiesController::class, 'getDepartments'])->name('faculties.departments');
Route::get('/departments/{id}/programs', [DepartmentsController::class, 'getPrograms'])->name('departments.programs');

/**
 * Authenticated Routes
 */
Route::middleware(['auth'])->group(function () {

    /**
     * Admin Routes
     */
    Route::prefix('admin')->name('admin.')->middleware('can:is-admin')->group(function () {

        // Admin Home Route
        Route::get('/home', [AdminHomeController::class, 'showHomePage'])->name('home');

        /**
         * Question Modules Routes
         */
        Route::prefix('question-modules')->name('question-modules.')->group(function () {
            Route::get('/', [QuestionModulesController::class, 'index'])->name('index');
            Route::get('/create', [QuestionModulesController::class, 'create'])->name('create');
            Route::post('/store', [QuestionModulesController::class, 'store'])->name('store');
            Route::delete('/{id}', [QuestionModulesController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [QuestionModulesController::class, 'getModuleDetailsWithQuestions'])->name('module');
            Route::get('/{module}/questions', [QuestionModulesController::class, 'getQuestions'])->name('questions');
        });

        /**
         * Questions Routes
         */
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::delete('/{id}', [QuestionsController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/options', [QuestionModulesController::class, 'getOptions'])->name('options');
        });

        /**
         * Questionnaires Routes
         */
        Route::prefix('questionnaires')->name('questionnaires.')->group(function () {
            Route::get('/', [QuestionnairesController::class, 'index'])->name('index');
            Route::get('/create', [QuestionnairesController::class, 'create'])->name('create');
            Route::post('/store', [QuestionnairesController::class, 'store'])->name('store');
            Route::get('/results', [QuestionnairesController::class, 'results'])->name('results');
            Route::get('/{id}/stats', [QuestionnairesController::class, 'showStats'])->name('stats');
            Route::get('/{id}/generate-report', [QuestionnairesController::class, 'generateReport'])
            ->name('generate-report');
        
        });
    });

    /**
     * Responder Routes
     */
    Route::prefix('responder')->name('responder.')->group(function () {

        // Responder Home Route
        Route::get('/home', [ResponderHomeController::class, 'index'])->name('home');

        // Responder Questionnaire Routes
        Route::get('/questionnaire/history', [ResponderQuestionnaireController::class, 'history'])->name('questionnaire.history');
        Route::get('/questionnaire/{id}', [ResponderQuestionnaireController::class, 'show'])->name('questionnaire.show');
        Route::post('/questionnaire/{id}/submit', [ResponderQuestionnaireController::class, 'submit'])->name('questionnaire.submit');
        Route::get('/questionnaire/{id}/completed', [ResponderQuestionnaireController::class, 'completed'])->name('questionnaire.completed');
    });
});
