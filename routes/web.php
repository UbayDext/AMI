<?php

use App\Http\Controllers\Assessor\AssessmentListController;
use App\Http\Controllers\Assessor\AssessmentShowController;
use App\Http\Controllers\Internal\PreparationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Assessor\AssessmentFillController;
use App\Http\Controllers\Assessor\FindingController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth'])->group(function () {

    Route::middleware('permission:view dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    });

    Route::prefix('admin')->name('admin.')->middleware('permission:manage assessments')->group(function () {
        Route::get('/assessments', [AdminAssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/create', [AdminAssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AdminAssessmentController::class, 'store'])->name('assessments.store');
        Route::get('/assessments/{assessment}', [AdminAssessmentController::class, 'show'])->name('assessments.show');
        Route::delete('/assessments/{assessment}', [AdminAssessmentController::class, 'destroy'])
            ->name('assessments.destroy');

    });

    Route::prefix('internal')
    ->name('internal.')
    ->middleware(['auth', 'permission:manage preparations'])
    ->group(function () {
        Route::get('/preparations', [PreparationController::class, 'index'])->name('preparations.index');
        Route::post('/preparations/tasks/{task}/upload', [PreparationController::class, 'upload'])->name('preparations.upload');
        Route::post('/preparations/tasks/{task}/toggle', [PreparationController::class, 'toggle'])->name('preparations.toggle');
        Route::delete('/preparations/files/{file}', [PreparationController::class, 'destroyFile'])->name('preparations.files.destroy');
    });

    Route::prefix('admin')->name('admin.')->middleware('permission:manage questions')->group(function () {
        Route::resource('questions', AdminQuestionController::class)->except(['show']);
    });

    Route::prefix('admin')->name('admin.')->middleware('permission:manage users')->group(function () {
    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');

    Route::get('/users/create', [UserRoleController::class, 'create'])->name('users.create');
    Route::post('/users', [UserRoleController::class, 'store'])->name('users.store');

    Route::put('/users/{user}', [UserRoleController::class, 'update'])->name('users.update');
});


    Route::prefix('assessor')->name('assessor.')->middleware('permission:fill assessment')->group(function () {
    Route::get('/assessments', [AssessmentListController::class, 'index'])->name('assessments.index');

    Route::get('/assessments/{assessment}/fill', [AssessmentFillController::class, 'edit'])->name('assessments.fill');
    Route::post('/assessments/{assessment}/fill', [AssessmentFillController::class, 'update'])->name('assessments.fill.update');

    Route::post('/assessments/{assessment}/findings', [FindingController::class, 'store'])->name('findings.store');
    Route::get('/assessments/{assessment}', [AssessmentShowController::class, 'show'])
    ->name('assessments.show');


});

});

require __DIR__.'/auth.php';
