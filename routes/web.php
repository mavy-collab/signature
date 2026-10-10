<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\VaultController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/verifier', [VerificationController::class, 'show'])->name('document-verification.show');
Route::post('/verifier', [VerificationController::class, 'verify'])->name('document-verification.check');

Route::get('/mentions-legales', [LegalController::class, 'notice'])->name('legal.notice');
Route::get('/cgu', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/confidentialite', [LegalController::class, 'privacy'])->name('legal.privacy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/signature', [SignatureController::class, 'show'])->name('signature.show');
    Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('/documents/sign-many', [DocumentController::class, 'signMany'])->name('documents.sign-many');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/file', [DocumentController::class, 'file'])->name('documents.file');
    Route::post('/documents/{document}/sign', [DocumentController::class, 'sign'])->name('documents.sign');

    Route::get('/historique', [HistoryController::class, 'index'])->name('history.index');
});

Route::middleware(['auth', 'password.confirm'])->prefix('coffre-fort')->name('vault.')->group(function () {
    Route::get('/', [VaultController::class, 'index'])->name('index');
    Route::get('/{document}', [VaultController::class, 'show'])->name('show');
    Route::get('/{document}/file', [VaultController::class, 'file'])->name('file');
    Route::get('/{document}/download', [VaultController::class, 'download'])->name('download');
});

Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('/signature/edit', [SignatureController::class, 'edit'])->name('signature.edit');
    Route::post('/signature/edit', [SignatureController::class, 'update'])->name('signature.update');
});

require __DIR__.'/auth.php';
