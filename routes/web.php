<?php

use App\Http\Controllers\Admin\AuditorDecreeController;
use App\Http\Controllers\Admin\QuestionCategoryController;
use App\Http\Controllers\Assessor\AssessmentListController;
use App\Http\Controllers\Assessor\AssessmentShowController;
use App\Http\Controllers\Admin\StandardPreparationController;
use App\Http\Controllers\Internal\PreparationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;

use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\FoskController;
use App\Http\Controllers\Admin\RoleRequestController as AdminRoleRequestController;
use App\Http\Controllers\Assessor\AssessmentFillController;
use App\Http\Controllers\Assessor\AssessmentReportController;
use App\Http\Controllers\Assessor\FindingController;
use App\Http\Controllers\Assessor\PtsController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\Admin\AmiCycleController;
use App\Http\Controllers\Internal\AmiSubmissionController;
use App\Http\Controllers\Assessor\AmiReviewController;
use App\Http\Controllers\Admin\AmiQuestionController;
use App\Http\Controllers\OnboardingController;

Route::get('/', function () {
    return view('auth.login');
});

// Public: shown when an assessor account is deactivated mid-session
Route::get('/deactivated', fn() => view('auth.deactivated'))->name('deactivated');

Route::middleware(['auth'])->group(function () {
    Route::middleware('permission:view dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
        Route::get('/dashboard/kategori', [DashboardController::class, 'kategoriData'])->name('dashboard.kategori');
    });
    Route::post('/onboarding/dashboard-admin/progress', [OnboardingController::class, 'update'])->name('onboarding.dashboard-admin.progress');
    Route::post('/onboarding/dashboard-admin/restart', [OnboardingController::class, 'restart'])->name('onboarding.dashboard-admin.restart');
    Route::post('/onboarding/admin-assessments/progress', [OnboardingController::class, 'assessmentUpdate'])->name('onboarding.admin-assessments.progress');
    Route::post('/onboarding/admin-assessments/restart', [OnboardingController::class, 'assessmentRestart'])->name('onboarding.admin-assessments.restart');
    Route::post('/onboarding/admin-ami-cycles/progress', [OnboardingController::class, 'amiCycleUpdate'])->name('onboarding.admin-ami-cycles.progress');
    Route::post('/onboarding/admin-ami-cycles/restart', [OnboardingController::class, 'amiCycleRestart'])->name('onboarding.admin-ami-cycles.restart');
    Route::post('/onboarding/admin-evidence-standards/progress', [OnboardingController::class, 'standardUpdate'])->name('onboarding.admin-evidence-standards.progress');
    Route::post('/onboarding/admin-evidence-standards/restart', [OnboardingController::class, 'standardRestart'])->name('onboarding.admin-evidence-standards.restart');
    Route::post('/onboarding/admin-ami-questions/progress', [OnboardingController::class, 'amiQuestionUpdate'])->name('onboarding.admin-ami-questions.progress');
    Route::post('/onboarding/admin-ami-questions/restart', [OnboardingController::class, 'amiQuestionRestart'])->name('onboarding.admin-ami-questions.restart');
    Route::post('/onboarding/dashboard-auditee/progress', [OnboardingController::class, 'auditeeDashboardUpdate'])->name('onboarding.dashboard-auditee.progress');
    Route::post('/onboarding/dashboard-auditee/restart', [OnboardingController::class, 'auditeeDashboardRestart'])->name('onboarding.dashboard-auditee.restart');
    Route::post('/onboarding/internal-preparations/progress', [OnboardingController::class, 'internalPreparationsUpdate'])->name('onboarding.internal-preparations.progress');
    Route::post('/onboarding/internal-preparations/restart', [OnboardingController::class, 'internalPreparationsRestart'])->name('onboarding.internal-preparations.restart');
    Route::post('/onboarding/internal-preparations-detail/progress', [OnboardingController::class, 'internalPreparationsDetailUpdate'])->name('onboarding.internal-preparations-detail.progress');
    Route::post('/onboarding/internal-preparations-detail/restart', [OnboardingController::class, 'internalPreparationsDetailRestart'])->name('onboarding.internal-preparations-detail.restart');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::middleware('permission:manage assessments')->group(function () {
                Route::get('/assessments', [AdminAssessmentController::class, 'index'])->name('assessments.index');
                Route::get('/assessments/create', [AdminAssessmentController::class, 'create'])->name('assessments.create');
                Route::post('/assessments', [AdminAssessmentController::class, 'store'])->name('assessments.store');
                Route::get('/assessments/{assessment}', [AdminAssessmentController::class, 'show'])->name('assessments.show');
                Route::delete('/assessments/{assessment}', [AdminAssessmentController::class, 'destroy'])->name('assessments.destroy');
            });

            Route::middleware('permission:manage questions')->group(function () {
                Route::resource('question-categories', \App\Http\Controllers\Admin\QuestionCategoryController::class)->except(['show']);
            });

            Route::middleware('permission:manage assessments')->group(function () {
                Route::resource('auditor-decrees', AuditorDecreeController::class)->except(['show']);
                Route::resource('accreditation-years', \App\Http\Controllers\Admin\AccreditationYearController::class);
            });

            Route::middleware('permission:manage preparations')->group(function () {
                Route::get('/preparations', [PreparationController::class, 'index'])->name('preparations.index');
                Route::post('/preparations/tasks/{task}/upload', [PreparationController::class, 'upload'])->name('preparations.upload');
                Route::post('/preparations/tasks/{task}/toggle', [PreparationController::class, 'toggle'])->name('preparations.toggle');
                Route::delete('/preparations/files/{file}', [PreparationController::class, 'destroyFile'])->name('preparations.files.destroy');
            });

            // Preparation per Standard (admin manages stages + tasks)
            Route::middleware('permission:manage preparations')->prefix('standards')->name('standard-preparations.')->group(function () {
                Route::get('/', [StandardPreparationController::class, 'landing'])->name('landing');
                Route::get('/{standard}/preparations', [StandardPreparationController::class, 'index'])->name('index');
                Route::post('/{standard}/stages', [StandardPreparationController::class, 'storeStage'])->name('stages.store');
                Route::delete('/{standard}/stages/{stage}', [StandardPreparationController::class, 'destroyStage'])->name('stages.destroy');
                Route::post('/{standard}/stages/{stage}/tasks', [StandardPreparationController::class, 'storeTask'])->name('tasks.store');
                Route::delete('/{standard}/stages/{stage}/tasks/{task}', [StandardPreparationController::class, 'destroyTask'])->name('tasks.destroy');
            });

            // FOSK (Akreditasi) Module
            Route::middleware('permission:manage preparations')->prefix('fosk')->name('fosk.')->group(function () {
                Route::get('/', [FoskController::class, 'index'])->name('index');
                Route::get('/{criteria}', [FoskController::class, 'show'])->name('show');
                Route::post('/{criteria}/documents', [FoskController::class, 'storeDocument'])->name('documents.store');
                Route::put('/documents/{document}', [FoskController::class, 'updateDocument'])->name('documents.update');
                Route::delete('/documents/{document}', [FoskController::class, 'destroyDocument'])->name('documents.destroy');
                Route::post('/documents/{document}/evidences', [FoskController::class, 'storeEvidence'])->name('evidences.store');
                Route::delete('/evidences/{evidence}', [FoskController::class, 'destroyEvidence'])->name('evidences.destroy');
            });

            Route::middleware('permission:manage questions')->group(function () {
                Route::resource('questions', AdminQuestionController::class)->except(['show']);
                Route::get('questions-preparation-tasks', [AdminQuestionController::class, 'preparationTasks'])->name('questions.preparation-tasks');
                Route::get('questions-checklist', [AdminQuestionController::class, 'checklistQuestions'])->name('questions.checklist');
                Route::get('questions/import-checklist', [AdminQuestionController::class, 'importChecklistForm'])->name('questions.import-checklist.form');
                Route::post('questions/import-checklist', [AdminQuestionController::class, 'importChecklist'])->name('questions.import-checklist');
                Route::get('ami/questions', [AmiQuestionController::class, 'index'])->name('ami.questions.index');
                Route::get('ami/questions/data', [AmiQuestionController::class, 'data'])->name('ami.questions.data');
                Route::post('ami/questions/import', [AmiQuestionController::class, 'upload'])->name('ami.questions.import');
                Route::post('ami/questions/import/{batch}/generate', [AmiQuestionController::class, 'generate'])->name('ami.questions.generate');
                Route::get('ami/questions/template', [AmiQuestionController::class, 'template'])->name('ami.questions.template');
            });

            Route::middleware('permission:manage users')->group(function () {
                Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
                Route::get('/users/create', [UserRoleController::class, 'create'])->name('users.create');
                Route::post('/users', [UserRoleController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [UserRoleController::class, 'edit'])->name('users.edit');
                Route::put('/users/{user}', [UserRoleController::class, 'update'])->name('users.update');
                Route::patch('/users/{user}/toggle-active', [UserRoleController::class, 'toggleActive'])->name('users.toggle-active');
                Route::patch('/users/{user}/unblock', [UserRoleController::class, 'unblock'])->name('users.unblock');
                Route::delete('/users/{user}', [UserRoleController::class, 'destroy'])->name('users.destroy');

                // Role Request management (admin)
                Route::get('/role-requests', [AdminRoleRequestController::class, 'index'])->name('role-requests.index');
                Route::patch('/role-requests/{roleRequest}/approve', [AdminRoleRequestController::class, 'approve'])->name('role-requests.approve');
                Route::patch('/role-requests/{roleRequest}/reject', [AdminRoleRequestController::class, 'reject'])->name('role-requests.reject');
            });
        });

    // Role Request (user self-service) — semua user yang sudah login
    Route::get('/role-request', [RoleRequestController::class, 'create'])->name('role-requests.create');
    Route::post('/role-request', [RoleRequestController::class, 'store'])->name('role-requests.store');
    Route::delete('/role-request/{roleRequest}', [RoleRequestController::class, 'destroy'])->name('role-requests.destroy');

    Route::prefix('assessor')
        ->name('assessor.')
        ->middleware('permission:fill assessment')
        ->group(function () {
            Route::get('/assessments', [AssessmentListController::class, 'index'])->name('assessments.index');
            Route::get('/assessments/{assessment}/fill', [AssessmentFillController::class, 'edit'])->name('assessments.fill');
            Route::post('/assessments/{assessment}/fill', [AssessmentFillController::class, 'update'])->name('assessments.fill.update');
            Route::post('/assessments/{assessment}/findings', [FindingController::class, 'store'])->name('findings.store');
            Route::get('/assessments/{assessment}/report', [AssessmentReportController::class, 'show'])->name('assessments.report');
            Route::post('/assessments/{assessment}/pts', [PtsController::class, 'update'])->name('assessments.pts.update');
            Route::get('/assessments/{assessment}', [AssessmentShowController::class, 'show'])->name('assessments.show');
        });

    Route::prefix('internal/preparations')->name('internal.preparations.')
        ->middleware('role:auditor|auditee')
        ->group(function () {
            Route::get('/', [PreparationController::class, 'index'])->name('index');
            Route::get('/standards/{standard}', [PreparationController::class, 'show'])->name('show');
            Route::post('/standards/{standard}/stages', [PreparationController::class, 'storeStage'])->name('stages.store');
            Route::put('/standards/{standard}/stages/{stage}', [PreparationController::class, 'updateStage'])->name('stages.update');
            Route::delete('/standards/{standard}/stages/{stage}', [PreparationController::class, 'destroyStage'])->name('stages.destroy');
            Route::post('/standards/{standard}/stages/{stage}/tasks', [PreparationController::class, 'storeTask'])->name('tasks.store');
            Route::put('/standards/{standard}/stages/{stage}/tasks/{task}', [PreparationController::class, 'updateTask'])->name('tasks.update');
            Route::delete('/standards/{standard}/stages/{stage}/tasks/{task}', [PreparationController::class, 'destroyTask'])->name('tasks.destroy');
            Route::post('/tasks/{task}/upload', [PreparationController::class, 'upload'])->name('upload');
            Route::post('/tasks/{task}/link', [PreparationController::class, 'storeLink'])->name('storeLink');
            Route::post('/tasks/{task}/toggle', [PreparationController::class, 'toggle'])->name('toggle');
            Route::delete('/files/{file}', [PreparationController::class, 'destroyFile'])->name('files.destroy');
        });

    // AMI — Admin manages cycles & submissions
    Route::prefix('admin/ami')->name('admin.ami.')->middleware('permission:manage assessments')->group(function () {
        Route::get('/cycles', [AmiCycleController::class, 'index'])->name('cycles.index');
        Route::post('/cycles', [AmiCycleController::class, 'store'])->name('cycles.store');
        Route::get('/cycles/{cycle}', [AmiCycleController::class, 'show'])->name('cycles.show');
        Route::patch('/cycles/{cycle}/status', [AmiCycleController::class, 'patch'])->name('cycles.patch');
        Route::delete('/cycles/{cycle}', [AmiCycleController::class, 'destroy'])->name('cycles.destroy');
        Route::post('/cycles/{cycle}/submissions', [AmiCycleController::class, 'storeSubmission'])->name('cycles.submissions.store');
        Route::delete('/cycles/{cycle}/submissions/{submission}', [AmiCycleController::class, 'destroySubmission'])->name('cycles.submissions.destroy');
        Route::post('/cycles/{cycle}/submissions/{submission}/assign', [AmiCycleController::class, 'assignReviewer'])->name('cycles.submissions.assign');
    });

    // AMI — Auditee prepares references & evidences per checklist question
    Route::prefix('internal/ami')->name('internal.ami.')->middleware('role:admin|auditee')->group(function () {
        Route::get('/', [AmiSubmissionController::class, 'index'])->name('index');
        Route::post('/store-submission', [AmiSubmissionController::class, 'storeSubmission'])->name('store-submission');
        Route::get('/{submission}', [AmiSubmissionController::class, 'show'])->name('show');
        Route::post('/{submission}/references', [AmiSubmissionController::class, 'storeReference'])->name('references.store');
        Route::delete('/{submission}/references/{reference}', [AmiSubmissionController::class, 'destroyReference'])->name('references.destroy');
        Route::post('/{submission}/evidences', [AmiSubmissionController::class, 'storeEvidence'])->name('evidences.store');
        Route::delete('/{submission}/evidences/{evidence}', [AmiSubmissionController::class, 'destroyEvidence'])->name('evidences.destroy');
        Route::post('/{submission}/submit', [AmiSubmissionController::class, 'submit'])->name('submit');
    });

    // AMI — Auditor fills review
    Route::prefix('assessor/ami')->name('assessor.ami.')->middleware('permission:fill assessment')->group(function () {
        Route::get('/', [AmiReviewController::class, 'index'])->name('index');
        Route::get('/{review}', [AmiReviewController::class, 'show'])->name('show');
        Route::post('/{review}/answers', [AmiReviewController::class, 'saveAnswers'])->name('answers.save');
        Route::post('/{review}/findings', [AmiReviewController::class, 'storeFinding'])->name('findings.store');
        Route::patch('/{review}/findings/{finding}/atl', [AmiReviewController::class, 'updateFindingAtl'])->name('findings.atl');
        Route::delete('/{review}/findings/{finding}', [AmiReviewController::class, 'destroyFinding'])->name('findings.destroy');
        Route::patch('/{review}/complete', [AmiReviewController::class, 'complete'])->name('complete');
    });
});

require __DIR__ . '/auth.php';
