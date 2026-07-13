<?php

use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\KPIController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\FieldController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * PUBLIC ROUTES (No Authentication Required)
 * ============================================
 */

Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

/**
 * ============================================
 * PROTECTED ROUTES (Authentication Required)
 * ============================================
 */

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/sessions', [AuthController::class, 'sessions']);
        Route::delete('/sessions/{id}', [AuthController::class, 'revokeSession']);

        // 2FA routes
        Route::post('/2fa/enable', [TwoFactorController::class, 'enableTwoFactor']);
        Route::post('/2fa/verify-setup', [TwoFactorController::class, 'verifyTwoFactorSetup']);
        Route::post('/2fa/verify', [TwoFactorController::class, 'verify2FACode']);
        Route::post('/2fa/disable', [TwoFactorController::class, 'disableTwoFactor']);
        Route::get('/2fa/status', [TwoFactorController::class, 'check2FAStatus']);

        // Password change
        Route::post('/change-password', [PasswordResetController::class, 'changePassword']);
    });

    // ========== PROJECTS ==========
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{project}', [ProjectController::class, 'show']);
        Route::put('/{project}', [ProjectController::class, 'update']);
        Route::delete('/{project}', [ProjectController::class, 'destroy']);
        Route::get('/{project}/dashboard', [ProjectController::class, 'dashboard']);
        Route::get('/{project}/risk-score', [ProjectController::class, 'riskScore']);
        Route::post('/{project}/duplicate', [ProjectController::class, 'duplicate']);

        // ========== ACTIVITIES (nested) ==========
        Route::prefix('{project}/activities')->group(function () {
            Route::get('/', [ActivityController::class, 'index']);
            Route::post('/', [ActivityController::class, 'store']);
            Route::put('/{activity}', [ActivityController::class, 'update']);
            Route::post('/{activity}/complete', [ActivityController::class, 'complete']);
            Route::post('/{activity}/block', [ActivityController::class, 'block']);
            Route::get('/{activity}/dependencies', [ActivityController::class, 'dependencies']);
            Route::delete('/{activity}', [ActivityController::class, 'destroy']);
        });

        // ========== KPIs (nested) ==========
        Route::prefix('{project}/kpis')->group(function () {
            Route::get('/', [KPIController::class, 'index']);
            Route::post('/', [KPIController::class, 'store']);
            Route::put('/{kpi}', [KPIController::class, 'update']);
            Route::post('/{kpi}/measures', [KPIController::class, 'addMeasure']);
            Route::get('/{kpi}/history', [KPIController::class, 'history']);
            Route::get('/{kpi}/performance', [KPIController::class, 'performance']);
            Route::delete('/{kpi}', [KPIController::class, 'destroy']);
        });

        // ========== FINANCE (nested) ==========
        Route::prefix('{project}/finance')->group(function () {
            Route::get('/budgets', [FinanceController::class, 'budgets']);
            Route::post('/budgets', [FinanceController::class, 'storeBudget']);
            Route::get('/summary', [FinanceController::class, 'summary']);
            Route::post('/budgets/{budget}/expenses', [FinanceController::class, 'submitExpense']);
            Route::put('/expenses/{expense}/validate', [FinanceController::class, 'validateExpense']);
            Route::put('/expenses/{expense}/reject', [FinanceController::class, 'rejectExpense']);
        });

        // ========== FIELD FORMS & SUBMISSIONS (nested) ==========
        Route::prefix('{project}/field')->group(function () {
            Route::get('/forms', [FieldController::class, 'listForms']);
            Route::post('/forms', [FieldController::class, 'storeForm']);
            Route::get('/forms/{form}', [FieldController::class, 'showForm']);
            Route::post('/forms/{form}/submit', [FieldController::class, 'submitData']);
            Route::get('/forms/{form}/submissions', [FieldController::class, 'getSubmissions']);
            Route::post('/submissions/{submission}/validate', [FieldController::class, 'validateSubmission']);
            Route::post('/submissions/{submission}/reject', [FieldController::class, 'rejectSubmission']);
            Route::get('/map-data', [FieldController::class, 'getLocationData']);
        });

        // ========== AI MODULE (nested under project) ==========
        Route::prefix('{project}/ai')->group(function () {
            Route::post('/analyze', [AIController::class, 'analyze']);
            Route::post('/predict-risks', [AIController::class, 'predictRisks']);
            Route::post('/budget-forecast', [AIController::class, 'budgetForecast']);
            Route::get('/recommendations', [AIController::class, 'recommendations']);
        });

        // ========== REPORTS (nested under project) ==========
        Route::prefix('{project}/reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/generate', [ReportController::class, 'generate']);
        });
    });

    // ========== BATCH SYNC (for offline support) ==========
    Route::post('/field/sync-batch', [FieldController::class, 'syncBatch']);

    // ========== AI GLOBAL ENDPOINTS ==========
    Route::prefix('ai')->group(function () {
        Route::post('/chat', [AIController::class, 'chat']);
        Route::delete('/recommendations/{recommendation}', [AIController::class, 'markRead']);
    });

    // ========== REPORTS GLOBAL ==========
    Route::prefix('reports')->group(function () {
        Route::get('/{report}/download', [ReportController::class, 'download']);
        Route::delete('/{report}', [ReportController::class, 'destroy']);
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status'    => 'healthy',
        'timestamp' => now(),
    ]);
});
