<?php

use Illuminate\Support\Facades\Route;
use App\Models\Document;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::get('/documents', function () {
    return view('dashboard');
})->name('documents.index');

Route::get('/documents/{document}', function (Document $document) {
    return view('documents.show', compact('document'));
})->name('documents.show');

    
    
require __DIR__.'/auth.php';
