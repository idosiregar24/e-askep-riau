<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CareSessionController;
use App\Http\Controllers\Api\V1\ClinicalInstructorController;
use App\Http\Controllers\Api\V1\MasterDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status'    => 'ok',
            'app'       => 'e-Askep Poltekkes Kemenkes Riau API',
            'version'   => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Public Auth
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Routes (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth User Profile
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Master Data
        Route::get('/master/courses', [MasterDataController::class, 'courses']);
        Route::get('/master/sdki-slki-siki', [MasterDataController::class, 'sdkiSlkiSiki']);
        Route::get('/master/spo-procedures', [MasterDataController::class, 'spoProcedures']);

        // Sesi Asuhan Mahasiswa
        Route::get('/sessions', [CareSessionController::class, 'index']);
        Route::post('/sessions', [CareSessionController::class, 'store']);
        Route::get('/sessions/{session}', [CareSessionController::class, 'show']);
        Route::put('/sessions/{session}/draft', [CareSessionController::class, 'saveDraft']);
        Route::post('/sessions/{session}/submit', [CareSessionController::class, 'submit']);

        // Workspace Dosen / CI
        Route::prefix('ci')->group(function () {
            Route::get('/submissions', [ClinicalInstructorController::class, 'submissions']);
            Route::post('/verify-procedures', [ClinicalInstructorController::class, 'verifyProcedures']);
            Route::post('/sessions/{session}/revision', [ClinicalInstructorController::class, 'requestRevision']);
            Route::post('/sessions/{session}/grade', [ClinicalInstructorController::class, 'gradeSession']);
        });
    });
});
