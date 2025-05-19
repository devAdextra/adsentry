<?php

use App\Http\Controllers\UploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScoringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/download-all-movements', [DashboardController::class, 'downloadAllMovements'])->name('dashboard.downloadAll');
Route::get('/dashboard/downloads', [DashboardController::class, 'listDownloads'])->name('dashboard.listDownloads');

Route::get('/upload', [UploadController::class, 'showUploadForm'])->name('upload.index');
Route::post('/upload', [UploadController::class, 'handleUpload'])->name('upload.handle');

Route::get('/scoring', [ScoringController::class, 'index'])->name('scoring.index');

use App\Http\Controllers\Api\FilterController;

Route::get('/filter-options', [FilterController::class, 'getFilterOptions'])->name('filter.options');

Route::get('/movements/monthly-stats', [App\Http\Controllers\Api\MovementController::class, 'monthlyStats']);

Route::get('/score/distribution', [\App\Http\Controllers\Api\ScoreController::class, 'getDistribution']);

Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::post('/downloads', [DownloadController::class, 'generate'])->name('downloads.generate');
Route::get('/downloads/{download}', [DownloadController::class, 'download'])->name('downloads.download');
Route::delete('/downloads/{download}', [DownloadController::class, 'destroy'])->name('downloads.destroy');
