<?php

use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DosenDashboardController;
use App\Http\Controllers\Web\DosenReviewController;
use App\Http\Controllers\Web\MahasiswaSessionController;
use App\Http\Controllers\Web\PrintController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing Welcome Page (Halaman Informasi Utama)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Web Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Web Workspace Routes
Route::middleware(['auth'])->group(function () {
    
    // Print Official Document
    Route::get('/print/case/{uuid}', [PrintController::class, 'show'])->name('print.case');

    // Dosen / Clinical Instructor (CI) Desk
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
        Route::get('/review/{uuid}', [DosenReviewController::class, 'show'])->name('review');
        Route::post('/review/{uuid}/verify-spo', [DosenReviewController::class, 'batchVerifySpo'])->name('verify-spo');
        Route::post('/review/{uuid}/request-revision', [DosenReviewController::class, 'requestRevision'])->name('request-revision');
        Route::post('/review/{uuid}/approve-grade', [DosenReviewController::class, 'approveAndGrade'])->name('approve-grade');
    });

    // Mahasiswa Clinical Cases
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaSessionController::class, 'index'])->name('dashboard');
        Route::get('/kasus/baru', [MahasiswaSessionController::class, 'create'])->name('create');
        Route::post('/kasus', [MahasiswaSessionController::class, 'store'])->name('store');
        Route::get('/kasus/{uuid}', [MahasiswaSessionController::class, 'show'])->name('show');
        Route::post('/kasus/{uuid}/assessment', [MahasiswaSessionController::class, 'updateAssessment'])->name('update-assessment');
        Route::post('/kasus/{uuid}/care-plan', [MahasiswaSessionController::class, 'storeCarePlan'])->name('store-care-plan');
        Route::delete('/kasus/{uuid}/care-plan/{planId}', [MahasiswaSessionController::class, 'destroyCarePlan'])->name('destroy-care-plan');
        Route::post('/kasus/{uuid}/procedure/{logId}/toggle', [MahasiswaSessionController::class, 'toggleProcedure'])->name('toggle-procedure');
        Route::post('/kasus/{uuid}/vital-sign', [MahasiswaSessionController::class, 'storeVitalSign'])->name('store-vital-sign');
        Route::post('/kasus/{uuid}/handover', [MahasiswaSessionController::class, 'storeHandover'])->name('store-handover');
        Route::post('/kasus/{uuid}/submit', [MahasiswaSessionController::class, 'submit'])->name('submit');
    });

    // Administrator
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('courses');
        Route::get('/groups', [AdminDashboardController::class, 'groups'])->name('groups');
        Route::post('/groups', [AdminDashboardController::class, 'storeGroup'])->name('groups.store');
        Route::get('/master-3s', [AdminDashboardController::class, 'master3s'])->name('master3s');
    });

});
