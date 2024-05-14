<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\DocumentController;
Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

require __DIR__.'/auth.php';
