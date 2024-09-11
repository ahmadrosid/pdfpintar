<?php

use Illuminate\Support\Facades\Route;
use App\Models\Document;
use Illuminate\Http\Request;

Route::view('/', 'welcome');

Route::get('/locale/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::group(['middleware' => 'auth'], function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('documents', 'dashboard')->name('documents.index');
    Route::view('profile', 'profile')->name('profile');

    Route::get('/documents/{document}', function (Request $request, Document $document) {
        if (config('app.require_email_verification') && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('documents.show', compact('document'));
    })->name('documents.show');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', __('Verification link sent!'));
    })
    ->middleware(['throttle:2,1'])
    ->name('verification.send');

    Route::supportBubble();
});

require __DIR__.'/auth.php';
