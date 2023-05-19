<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/documents', DocumentController::class);
    Route::resource("/document/chat", DocumentChatController::class);
    Route::get("/document/chat/streaming/{chat_id}", [DocumentChatController::class, "streaming"]);
});


require __DIR__ . '/auth.php';
