<?php

use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DosenDashboardController;
use App\Http\Controllers\Web\DosenReviewController;
use App\Http\Controllers\Web\InstrumentExportController;
use App\Http\Controllers\Web\MahasiswaSessionController;
use App\Http\Controllers\Web\PrintController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

// Landing Welcome Page (Halaman Informasi Utama)
Route::get('/', function () {
    return Inertia::render('Welcome');
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

        // Ekspor Instrumen Penilaian Klinik (tata letak resmi Poltekkes Riau)
        Route::get('/review/{uuid}/instrumen/pdf', [InstrumentExportController::class, 'pdf'])->name('instrumen.pdf');
        Route::get('/review/{uuid}/instrumen/word', [InstrumentExportController::class, 'word'])->name('instrumen.word');
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

        // Unduh instrumen penilaian miliknya sendiri (hanya setelah berkas disahkan)
        Route::get('/kasus/{uuid}/instrumen/pdf', [InstrumentExportController::class, 'pdf'])->name('instrumen.pdf');
        Route::get('/kasus/{uuid}/instrumen/word', [InstrumentExportController::class, 'word'])->name('instrumen.word');
    });

    // Administrator
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users Management
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');

        // Courses Management
        Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('courses');
        Route::post('/courses', [AdminDashboardController::class, 'storeCourse'])->name('courses.store');
        Route::put('/courses/{id}', [AdminDashboardController::class, 'updateCourse'])->name('courses.update');
        Route::delete('/courses/{id}', [AdminDashboardController::class, 'destroyCourse'])->name('courses.destroy');

        // Groups Management
        Route::get('/groups', [AdminDashboardController::class, 'groups'])->name('groups');
        Route::post('/groups', [AdminDashboardController::class, 'storeGroup'])->name('groups.store');
        Route::put('/groups/{id}', [AdminDashboardController::class, 'updateGroup'])->name('groups.update');
        Route::delete('/groups/{id}', [AdminDashboardController::class, 'destroyGroup'])->name('groups.destroy');

        // Master 3S PPNI Management
        Route::get('/master-3s', [AdminDashboardController::class, 'master3s'])->name('master3s');
        Route::post('/master-sdki', [AdminDashboardController::class, 'storeSdki'])->name('sdki.store');
        Route::put('/master-sdki/{id}', [AdminDashboardController::class, 'updateSdki'])->name('sdki.update');
        Route::delete('/master-sdki/{id}', [AdminDashboardController::class, 'destroySdki'])->name('sdki.destroy');

        Route::post('/master-slki', [AdminDashboardController::class, 'storeSlki'])->name('slki.store');
        Route::put('/master-slki/{id}', [AdminDashboardController::class, 'updateSlki'])->name('slki.update');
        Route::delete('/master-slki/{id}', [AdminDashboardController::class, 'destroySlki'])->name('slki.destroy');

        Route::post('/master-siki', [AdminDashboardController::class, 'storeSiki'])->name('siki.store');
        Route::put('/master-siki/{id}', [AdminDashboardController::class, 'updateSiki'])->name('siki.update');
        Route::delete('/master-siki/{id}', [AdminDashboardController::class, 'destroySiki'])->name('siki.destroy');

        // Master SPO Procedures Management
        Route::get('/spo', [AdminDashboardController::class, 'spo'])->name('spo');
        Route::post('/spo', [AdminDashboardController::class, 'storeSpo'])->name('spo.store');
        Route::put('/spo/{id}', [AdminDashboardController::class, 'updateSpo'])->name('spo.update');
        Route::delete('/spo/{id}', [AdminDashboardController::class, 'destroySpo'])->name('spo.destroy');
    });

});
