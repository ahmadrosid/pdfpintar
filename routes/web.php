<?php

use Illuminate\Support\Facades\Route;
use App\Models\Document;

Route::view('/', 'welcome');

Route::group(['middleware' => 'auth'], function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('documents', 'dashboard')->name('documents.index');
    Route::view('profile', 'profile')->name('profile');

    Route::get('/documents/{document}', function (Document $document) {
        return view('documents.show', compact('document'));
    })->name('documents.show');
});

require __DIR__.'/auth.php';
