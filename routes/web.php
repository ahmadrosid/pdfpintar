<?php

use App\Livewire\MergeDocumentPdf;
use App\Livewire\ToolIndex;
use Illuminate\Support\Facades\Route;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome');

Route::get('/locale/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::get('tools', ToolIndex::class)->name('tools.index');
Route::get('tools/merge-pdf', MergeDocumentPdf::class)->name('tools.merge-pdf');
Route::view('tools/pdf-generator', 'pdf-generator')->name('tools.pdf-generator');

Route::group(['middleware' => 'auth'], function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('documents', 'dashboard')->name('documents.index');
    Route::view('profile', 'profile')->name('profile');

    Route::get('/documents/{document}', function (Request $request, Document $document) {
        if (config('app.require_email_verification') && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        $pdfUrl = Storage::temporaryUrl(
            $document->file_path, now()->addMinutes(5)
        );
        return view('documents.show', compact('document', 'pdfUrl'));
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
