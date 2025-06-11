<?php

use App\Http\Controllers\UploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScoringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\MovementController;
use App\Http\Controllers\Api\ScoreController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;

// Rotte pubbliche
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.index');
    }
    return view('welcome');
});

// Rotte di autenticazione (solo login, no registrazione)
Auth::routes([
    'register' => false, // Disabilita la registrazione
    'reset' => true,     // Mantiene il reset password
    'verify' => false    // Disabilita la verifica email
]);

// Rotte protette (richiedono autenticazione)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/download-all-movements', [DashboardController::class, 'downloadAllMovements'])->name('dashboard.downloadAll');
    Route::get('/dashboard/downloads', [DashboardController::class, 'listDownloads'])->name('dashboard.listDownloads');

    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::get('/upload/{upload}/download', [UploadController::class, 'download'])->name('upload.download');
    Route::delete('/upload/{upload}', [UploadController::class, 'destroy'])->name('upload.destroy');

    Route::get('/upload/manual', [UploadController::class, 'manualUploads'])->name('upload.manual');
    Route::post('/upload/manual/process', [UploadController::class, 'processManualUpload'])->name('upload.manual.process');

    Route::get('/scoring', [ScoringController::class, 'index'])->name('scoring.index');

    Route::get('/filter-options', [FilterController::class, 'getFilterOptions'])->name('filter.options');
    Route::get('/movements/monthly-stats', [MovementController::class, 'monthlyStats']);
    Route::get('/score/distribution', [ScoreController::class, 'getDistribution']);
    Route::get('/score/db-distribution', [ScoreController::class, 'dbDistributionByScore']);

    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
    Route::post('/downloads', [DownloadController::class, 'generate'])->name('downloads.generate');
    Route::get('/downloads/{download}', [DownloadController::class, 'download'])->name('downloads.download');
    Route::delete('/downloads/{download}', [DownloadController::class, 'destroy'])->name('downloads.destroy');

    Route::post('/upload/process', [UploadController::class, 'processFile'])->name('upload.process');
    Route::get('/upload/progress/{filename}', [UploadController::class, 'getProgress'])->name('upload.progress');
});

// Rotte per la gestione degli utenti (solo admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
